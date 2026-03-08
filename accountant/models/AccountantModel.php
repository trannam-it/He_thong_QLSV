<?php
/**
 * AccountantModel - Model cho Kế toán
 * Quản lý học phí, học bổng, báo cáo tài chính
 */
class AccountantModel
{
    protected mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    // ══════════════════════════════════════════════════════
    // USER INFO
    // ══════════════════════════════════════════════════════

    public function getUserInfo(int $userId): array
    {
        $stmt = $this->conn->prepare("
            SELECT u.id AS user_id, u.username, u.email,
                   r.name AS role_name, r.code AS role_code
            FROM users u
            LEFT JOIN user_roles ur ON ur.user_id = u.id
            LEFT JOIN roles r ON r.id = ur.role_id
            WHERE u.id = ? LIMIT 1
        ");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row ?? ['user_id' => $userId, 'username' => 'N/A', 'email' => '', 'role_name' => 'Kế toán', 'role_code' => 'accountant'];
    }

    // ══════════════════════════════════════════════════════
    // DASHBOARD
    // ══════════════════════════════════════════════════════

    public function getDashboardStats(): array
    {
        $stats = [];

        $res = $this->conn->query("SELECT COUNT(*) AS cnt FROM tuition_invoices WHERE status='Unpaid'");
        $stats['unpaid_invoices'] = (int)$res->fetch_assoc()['cnt'];

        $res = $this->conn->query("SELECT COUNT(*) AS cnt FROM tuition_invoices WHERE status='Overdue'");
        $stats['overdue_invoices'] = (int)$res->fetch_assoc()['cnt'];

        $res = $this->conn->query("SELECT SUM(amount_due) AS total FROM tuition_invoices WHERE status IN ('Unpaid','Partial','Overdue')");
        $stats['total_receivable'] = (float)($res->fetch_assoc()['total'] ?? 0);

        $res = $this->conn->query("SELECT SUM(amount_paid) AS total FROM tuition_invoices");
        $stats['total_collected'] = (float)($res->fetch_assoc()['total'] ?? 0);

        $res = $this->conn->query("SELECT COUNT(*) AS cnt FROM scholarship_applications WHERE status='Approved'");
        $stats['approved_scholarships'] = (int)$res->fetch_assoc()['cnt'];

        $res = $this->conn->query("SELECT COUNT(*) AS cnt FROM scholarship_applications WHERE status='Pending'");
        $stats['pending_scholarship_apps'] = (int)$res->fetch_assoc()['cnt'];

        $res = $this->conn->query("SELECT SUM(s.value) AS total FROM scholarship_applications sa JOIN scholarships s ON s.scholarship_id = sa.scholarship_id WHERE sa.status='Approved'");
        $stats['total_scholarship_amount'] = (float)($res->fetch_assoc()['total'] ?? 0);

        return $stats;
    }

    public function getRecentInvoices(int $limit = 10): array
    {
        $stmt = $this->conn->prepare("
            SELECT ti.invoice_id, ti.semester, ti.year, ti.amount_due, ti.amount_paid, ti.status, ti.due_date,
                   CONCAT(s.first_name,' ',s.last_name) AS student_name, s.student_code,
                   f.faculty_name
            FROM tuition_invoices ti
            JOIN students s ON s.student_id = ti.student_id
            LEFT JOIN faculties f ON f.faculty_id = s.faculty_id
            ORDER BY ti.created_at DESC
            LIMIT ?
        ");
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // ══════════════════════════════════════════════════════
    // TUITION (HỌC PHÍ)
    // ══════════════════════════════════════════════════════

    public function getAllInvoices(string $status = '', string $search = '', string $semester = '', int $year = 0): array
    {
        $sql = "
            SELECT ti.invoice_id, ti.semester, ti.year, ti.total_credits,
                   ti.amount_due, ti.amount_paid, ti.status, ti.due_date, ti.paid_at, ti.note,
                   CONCAT(s.first_name,' ',s.last_name) AS student_name, s.student_code, s.student_id,
                   f.faculty_name
            FROM tuition_invoices ti
            JOIN students s ON s.student_id = ti.student_id
            LEFT JOIN faculties f ON f.faculty_id = s.faculty_id
            WHERE 1=1
        ";
        $params = []; $types = '';

        if ($status !== '') {
            $sql .= " AND ti.status = ?";
            $params[] = $status; $types .= 's';
        }
        if ($search !== '') {
            $like = '%' . $search . '%';
            $sql .= " AND (s.student_code LIKE ? OR CONCAT(s.first_name,' ',s.last_name) LIKE ?)";
            $params[] = $like; $params[] = $like; $types .= 'ss';
        }
        if ($semester !== '') {
            $sql .= " AND ti.semester = ?";
            $params[] = $semester; $types .= 's';
        }
        if ($year > 0) {
            $sql .= " AND ti.year = ?";
            $params[] = $year; $types .= 'i';
        }
        $sql .= " ORDER BY ti.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        if ($params) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getInvoiceById(int $id): ?array
    {
        $stmt = $this->conn->prepare("
            SELECT ti.*, CONCAT(s.first_name,' ',s.last_name) AS student_name,
                   s.student_code, s.email AS student_email,
                   f.faculty_name
            FROM tuition_invoices ti
            JOIN students s ON s.student_id = ti.student_id
            LEFT JOIN faculties f ON f.faculty_id = s.faculty_id
            WHERE ti.invoice_id = ? LIMIT 1
        ");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function updateInvoiceStatus(int $invoiceId, string $status, string $note = ''): bool
    {
        $stmt = $this->conn->prepare("UPDATE tuition_invoices SET status = ?, note = ? WHERE invoice_id = ?");
        $stmt->bind_param('ssi', $status, $note, $invoiceId);
        return $stmt->execute();
    }

    public function recordPayment(int $invoiceId, float $amount): bool
    {
        $inv = $this->getInvoiceById($invoiceId);
        if (!$inv) return false;

        $newPaid = (float)$inv['amount_paid'] + $amount;
        $amountDue = (float)$inv['amount_due'];
        $status = 'Partial';
        if ($newPaid >= $amountDue) {
            $status = 'Paid';
        }

        $stmt = $this->conn->prepare("
            UPDATE tuition_invoices
            SET amount_paid = ?, status = ?,
                paid_at = CASE WHEN ? >= amount_due THEN NOW() ELSE paid_at END
            WHERE invoice_id = ?
        ");
        $stmt->bind_param('dsdi', $newPaid, $status, $newPaid, $invoiceId);
        return $stmt->execute();
    }

    public function generateInvoiceForStudent(int $studentId, string $semester, int $year): bool
    {
        // Tính số tín chỉ đang học trong kỳ
        $stmt = $this->conn->prepare("
            SELECT COALESCE(SUM(sub.credits), 0) AS total_credits
            FROM enrollments e
            JOIN classes c ON c.class_id = e.class_id
            JOIN subjects sub ON sub.subject_id = c.subject_id
            JOIN semesters sem ON sem.semester_id = c.semester_id
            WHERE e.student_id = ? AND sem.semester = ? AND sem.year = ?
              AND e.status = 'Studying'
        ");
        $stmt->bind_param('isi', $studentId, $semester, $year);
        $stmt->execute();
        $credits = (int)$stmt->get_result()->fetch_assoc()['total_credits'];

        // Lấy giá tín chỉ
        $stmt = $this->conn->prepare("SELECT price_per_credit FROM tuition_settings WHERE semester = ? AND year = ? LIMIT 1");
        $stmt->bind_param('si', $semester, $year);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $price = $row ? (float)$row['price_per_credit'] : 500000;

        $amountDue = $credits * $price;
        $dueDate   = date('Y-m-d', strtotime("+30 days"));

        $stmt = $this->conn->prepare("
            INSERT IGNORE INTO tuition_invoices
                (student_id, semester, year, total_credits, amount_due, status, due_date)
            VALUES (?, ?, ?, ?, ?, 'Unpaid', ?)
        ");
        $stmt->bind_param('isiids', $studentId, $semester, $year, $credits, $amountDue, $dueDate);
        return $stmt->execute();
    }

    public function getTuitionSettings(): array
    {
        $result = $this->conn->query("SELECT * FROM tuition_settings ORDER BY year DESC, semester");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function updateTuitionSetting(string $semester, int $year, float $price, string $note = ''): bool
    {
        $stmt = $this->conn->prepare("
            INSERT INTO tuition_settings (semester, year, price_per_credit, note)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE price_per_credit = ?, note = ?
        ");
        $stmt->bind_param('sidsds', $semester, $year, $price, $note, $price, $note);
        return $stmt->execute();
    }

    // ══════════════════════════════════════════════════════
    // SCHOLARSHIPS (HỌC BỔNG)
    // ══════════════════════════════════════════════════════

    public function getAllScholarships(): array
    {
        $result = $this->conn->query("
            SELECT s.*,
                   (SELECT COUNT(*) FROM scholarship_applications sa WHERE sa.scholarship_id = s.scholarship_id) AS total_apps,
                   (SELECT COUNT(*) FROM scholarship_applications sa WHERE sa.scholarship_id = s.scholarship_id AND sa.status='Approved') AS approved_apps
            FROM scholarships s
            ORDER BY s.year DESC, s.semester, s.name
        ");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getScholarshipApplications(int $scholarshipId = 0, string $status = ''): array
    {
        $sql = "
            SELECT sa.application_id, sa.status, sa.applied_at, sa.reviewed_at, sa.note,
                   sch.name AS scholarship_name, sch.value, sch.semester, sch.year,
                   CONCAT(s.first_name,' ',s.last_name) AS student_name, s.student_code,
                   f.faculty_name
            FROM scholarship_applications sa
            JOIN scholarships sch ON sch.scholarship_id = sa.scholarship_id
            JOIN students s ON s.student_id = sa.student_id
            LEFT JOIN faculties f ON f.faculty_id = s.faculty_id
            WHERE 1=1
        ";
        $params = []; $types = '';

        if ($scholarshipId > 0) {
            $sql .= " AND sa.scholarship_id = ?";
            $params[] = $scholarshipId; $types .= 'i';
        }
        if ($status !== '') {
            $sql .= " AND sa.status = ?";
            $params[] = $status; $types .= 's';
        }
        $sql .= " ORDER BY sa.applied_at DESC";
        $stmt = $this->conn->prepare($sql);
        if ($params) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function reviewApplication(int $applicationId, string $status, string $note = ''): bool
    {
        $stmt = $this->conn->prepare("
            UPDATE scholarship_applications
            SET status = ?, note = ?, reviewed_at = NOW()
            WHERE application_id = ?
        ");
        $stmt->bind_param('ssi', $status, $note, $applicationId);
        return $stmt->execute();
    }

    public function createScholarship(array $data): bool
    {
        $stmt = $this->conn->prepare("
            INSERT INTO scholarships (name, description, value, min_gpa, max_gpa, semester, year, quantity, deadline, is_active)
            VALUES (?,?,?,?,?,?,?,?,?,?)
        ");
        $stmt->bind_param('ssdddsiisi',
            $data['name'], $data['description'], $data['value'],
            $data['min_gpa'], $data['max_gpa'], $data['semester'],
            $data['year'], $data['quantity'], $data['deadline'], $data['is_active']
        );
        return $stmt->execute();
    }

    public function toggleScholarshipActive(int $id): bool
    {
        $stmt = $this->conn->prepare("UPDATE scholarships SET is_active = 1 - is_active WHERE scholarship_id = ?");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    // ══════════════════════════════════════════════════════
    // STUDENTS
    // ══════════════════════════════════════════════════════

    public function getStudentsWithTuition(string $search = '', string $status = ''): array
    {
        $sql = "
            SELECT s.student_id, s.student_code,
                   CONCAT(s.first_name,' ',s.last_name) AS full_name,
                   s.email, s.status AS student_status,
                   f.faculty_name,
                   COUNT(ti.invoice_id) AS total_invoices,
                   SUM(ti.amount_due) AS total_due,
                   SUM(ti.amount_paid) AS total_paid,
                   SUM(CASE WHEN ti.status IN ('Unpaid','Overdue') THEN ti.amount_due - ti.amount_paid ELSE 0 END) AS outstanding
            FROM students s
            LEFT JOIN faculties f ON f.faculty_id = s.faculty_id
            LEFT JOIN tuition_invoices ti ON ti.student_id = s.student_id
            WHERE 1=1
        ";
        $params = []; $types = '';

        if ($search !== '') {
            $like = '%' . $search . '%';
            $sql .= " AND (s.student_code LIKE ? OR CONCAT(s.first_name,' ',s.last_name) LIKE ?)";
            $params[] = $like; $params[] = $like; $types .= 'ss';
        }
        if ($status !== '') {
            $sql .= " AND s.status = ?";
            $params[] = $status; $types .= 's';
        }

        $sql .= " GROUP BY s.student_id ORDER BY outstanding DESC, s.student_code";
        $stmt = $this->conn->prepare($sql);
        if ($params) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getStudentInvoices(int $studentId): array
    {
        $stmt = $this->conn->prepare("
            SELECT * FROM tuition_invoices WHERE student_id = ? ORDER BY year DESC, semester
        ");
        $stmt->bind_param('i', $studentId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // ══════════════════════════════════════════════════════
    // REPORTS
    // ══════════════════════════════════════════════════════

    public function getTuitionReportBySemester(): array
    {
        $result = $this->conn->query("
            SELECT ti.semester, ti.year,
                   COUNT(*) AS total_invoices,
                   SUM(ti.amount_due) AS total_due,
                   SUM(ti.amount_paid) AS total_paid,
                   SUM(ti.amount_due - ti.amount_paid) AS outstanding,
                   COUNT(CASE WHEN ti.status = 'Paid' THEN 1 END) AS paid_count,
                   COUNT(CASE WHEN ti.status IN ('Unpaid','Overdue') THEN 1 END) AS unpaid_count
            FROM tuition_invoices ti
            GROUP BY ti.semester, ti.year
            ORDER BY ti.year DESC, ti.semester
        ");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getScholarshipFinancialSummary(): array
    {
        $result = $this->conn->query("
            SELECT sch.semester, sch.year, sch.name,
                   COUNT(sa.application_id) AS total_apps,
                   COUNT(CASE WHEN sa.status='Approved' THEN 1 END) AS approved,
                   SUM(CASE WHEN sa.status='Approved' THEN sch.value ELSE 0 END) AS total_disbursed
            FROM scholarships sch
            LEFT JOIN scholarship_applications sa ON sa.scholarship_id = sch.scholarship_id
            GROUP BY sch.scholarship_id
            ORDER BY sch.year DESC, sch.semester, sch.name
        ");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // ══════════════════════════════════════════════════════
    // PROFILE
    // ══════════════════════════════════════════════════════

    public function updateUserProfile(int $userId, string $email): bool
    {
        $stmt = $this->conn->prepare("UPDATE users SET email = ? WHERE id = ?");
        $stmt->bind_param('si', $email, $userId);
        return $stmt->execute();
    }

    public function changePassword(int $userId, string $newHash): bool
    {
        $stmt = $this->conn->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        $stmt->bind_param('si', $newHash, $userId);
        return $stmt->execute();
    }

    public function getPasswordHash(int $userId): ?string
    {
        $stmt = $this->conn->prepare("SELECT password_hash FROM users WHERE id = ?");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row['password_hash'] ?? null;
    }

    // ══════════════════════════════════════════════════════
    // HELPERS
    // ══════════════════════════════════════════════════════

    public function getAllFaculties(): array
    {
        $result = $this->conn->query("SELECT faculty_id, faculty_name FROM faculties ORDER BY faculty_name");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getAvailableYears(): array
    {
        $result = $this->conn->query("SELECT DISTINCT year FROM tuition_invoices ORDER BY year DESC");
        return array_column($result->fetch_all(MYSQLI_ASSOC), 'year');
    }
}
