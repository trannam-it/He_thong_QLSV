<?php
/**
 * LibrarianModel - Model cho Thủ thư
 * Quản lý sách, mượn/trả sách, bạn đọc
 */
class LibrarianModel
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
        return $row ?? ['user_id' => $userId, 'username' => 'N/A', 'email' => '', 'role_name' => 'Thủ thư', 'role_code' => 'librarian'];
    }

    // ══════════════════════════════════════════════════════
    // DASHBOARD
    // ══════════════════════════════════════════════════════

    public function getDashboardStats(): array
    {
        $stats = [];

        $res = $this->conn->query("SELECT COUNT(*) AS cnt FROM library_books");
        $stats['total_books'] = (int)$res->fetch_assoc()['cnt'];

        $res = $this->conn->query("SELECT SUM(total_copies) AS tc, SUM(available_copies) AS ac FROM library_books");
        $row = $res->fetch_assoc();
        $stats['total_copies']     = (int)($row['tc'] ?? 0);
        $stats['available_copies'] = (int)($row['ac'] ?? 0);

        $res = $this->conn->query("SELECT COUNT(*) AS cnt FROM library_borrows WHERE status IN ('Borrowed','Overdue')");
        $stats['active_borrows'] = (int)$res->fetch_assoc()['cnt'];

        $res = $this->conn->query("SELECT COUNT(*) AS cnt FROM library_borrows WHERE status = 'Overdue'");
        $stats['overdue_count'] = (int)$res->fetch_assoc()['cnt'];

        $res = $this->conn->query("SELECT COUNT(*) AS cnt FROM library_borrows WHERE DATE(borrow_date) = CURDATE()");
        $stats['today_borrows'] = (int)$res->fetch_assoc()['cnt'];

        $res = $this->conn->query("SELECT COUNT(*) AS cnt FROM library_borrows WHERE DATE(return_date) = CURDATE()");
        $stats['today_returns'] = (int)$res->fetch_assoc()['cnt'];

        return $stats;
    }

    // public function getRecentBorrows(int $limit = 8): array
    // {
    //     $stmt = $this->conn->prepare("
    //         SELECT lb.borrow_id, lb.borrow_date, lb.due_date, lb.return_date,
    //                lb.status, lb.fine_amount,
    //                b.title, b.author,
    //                CONCAT(s.first_name,' ',s.last_name) AS student_name,
    //                s.student_code
    //         FROM library_borrows lb
    //         JOIN library_books b ON b.book_id = lb.book_id
    //         JOIN students s ON s.student_id = lb.student_id
    //         ORDER BY lb.created_at DESC
    //         LIMIT ?
    //     ");
    //     $stmt->bind_param('i', $limit);
    //     $stmt->execute();
    //     return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    // }

    public function getRecentBorrows(int $limit = 8): array
    {
        $sql = "
            SELECT lb.borrow_id, lb.borrow_date, lb.due_date, lb.return_date,
                lb.status,
                b.title, b.author,
                CONCAT(s.first_name,' ',s.last_name) AS student_name,
                s.student_code
            FROM library_borrows lb
            JOIN library_books b ON b.book_id = lb.book_id
            JOIN students s ON s.student_id = lb.student_id
            ORDER BY lb.borrow_date DESC
            LIMIT ?
        ";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            die("SQL Error: " . $this->conn->error);
        }

        $stmt->bind_param('i', $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}


    // ══════════════════════════════════════════════════════
    // BOOKS
    // ══════════════════════════════════════════════════════

    public function getAllBooks(string $search = '', string $category = ''): array
    {
        $sql = "SELECT * FROM library_books WHERE 1=1";
        $params = []; $types = '';

        if ($search !== '') {
            $like = '%' . $search . '%';
            $sql .= " AND (title LIKE ? OR author LIKE ? OR isbn LIKE ?)";
            $params[] = $like; $params[] = $like; $params[] = $like; $types .= 'sss';
        }
        if ($category !== '') {
            $sql .= " AND category = ?";
            $params[] = $category; $types .= 's';
        }
        $sql .= " ORDER BY title ASC";
        $stmt = $this->conn->prepare($sql);
        if ($params) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getBookById(int $id): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM library_books WHERE book_id = ? LIMIT 1");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function getAllCategories(): array
    {
        $result = $this->conn->query("SELECT DISTINCT category FROM library_books WHERE category IS NOT NULL ORDER BY category");
        return array_column($result->fetch_all(MYSQLI_ASSOC), 'category');
    }

    public function createBook(array $data): bool
    {
        $stmt = $this->conn->prepare("
            INSERT INTO library_books (title, author, isbn, category, publisher, published_year, total_copies, available_copies, description)
            VALUES (?,?,?,?,?,?,?,?,?)
        ");
        $stmt->bind_param('ssssssiis',
            $data['title'], $data['author'], $data['isbn'],
            $data['category'], $data['publisher'], $data['published_year'],
            $data['total_copies'], $data['available_copies'], $data['description']
        );
        return $stmt->execute();
    }

    public function updateBook(int $id, array $data): bool
    {
        $stmt = $this->conn->prepare("
            UPDATE library_books SET title=?, author=?, isbn=?, category=?, publisher=?,
            published_year=?, total_copies=?, available_copies=?, description=?
            WHERE book_id=?
        ");
        $stmt->bind_param('ssssssiisi',
            $data['title'], $data['author'], $data['isbn'],
            $data['category'], $data['publisher'], $data['published_year'],
            $data['total_copies'], $data['available_copies'], $data['description'], $id
        );
        return $stmt->execute();
    }

    public function deleteBook(int $id): bool
    {
        // Kiểm tra còn đang mượn không
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS cnt FROM library_borrows WHERE book_id = ? AND status IN ('Borrowed','Overdue')");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $cnt = (int)$stmt->get_result()->fetch_assoc()['cnt'];
        if ($cnt > 0) return false;

        $stmt = $this->conn->prepare("DELETE FROM library_books WHERE book_id = ?");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    public function getBookBorrowHistory(int $bookId): array
    {
        $stmt = $this->conn->prepare("
            SELECT lb.*, CONCAT(s.first_name,' ',s.last_name) AS student_name, s.student_code
            FROM library_borrows lb
            JOIN students s ON s.student_id = lb.student_id
            WHERE lb.book_id = ?
            ORDER BY lb.borrow_date DESC
        ");
        $stmt->bind_param('i', $bookId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // ══════════════════════════════════════════════════════
    // BORROWS
    // ══════════════════════════════════════════════════════

    public function getAllBorrows(string $status = '', string $search = ''): array
    {
        $sql = "
            SELECT lb.borrow_id, lb.borrow_date, lb.due_date, lb.return_date,
                   lb.status, lb.fine_amount, lb.note,
                   b.book_id, b.title, b.author,
                   CONCAT(s.first_name,' ',s.last_name) AS student_name,
                   s.student_code, s.student_id
            FROM library_borrows lb
            JOIN library_books b ON b.book_id = lb.book_id
            JOIN students s ON s.student_id = lb.student_id
            WHERE 1=1
        ";
        $params = []; $types = '';

        if ($status !== '') {
            $sql .= " AND lb.status = ?";
            $params[] = $status; $types .= 's';
        }
        if ($search !== '') {
            $like = '%' . $search . '%';
            $sql .= " AND (b.title LIKE ? OR s.student_code LIKE ? OR CONCAT(s.first_name,' ',s.last_name) LIKE ?)";
            $params[] = $like; $params[] = $like; $params[] = $like; $types .= 'sss';
        }
        $sql .= " ORDER BY lb.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        if ($params) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getBorrowById(int $id): ?array
    {
        $stmt = $this->conn->prepare("
            SELECT lb.*, b.title, b.author, b.isbn,
                   CONCAT(s.first_name,' ',s.last_name) AS student_name,
                   s.student_code, s.email AS student_email
            FROM library_borrows lb
            JOIN library_books b ON b.book_id = lb.book_id
            JOIN students s ON s.student_id = lb.student_id
            WHERE lb.borrow_id = ? LIMIT 1
        ");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function createBorrow(int $studentId, int $bookId, int $dueDays = 14): bool
    {
        $dueDate = date('Y-m-d', strtotime("+{$dueDays} days"));

        // Giảm available_copies
        $stmt = $this->conn->prepare("
            UPDATE library_books SET available_copies = available_copies - 1
            WHERE book_id = ? AND available_copies > 0
        ");
        $stmt->bind_param('i', $bookId);
        $stmt->execute();
        if ($stmt->affected_rows === 0) return false;

        $stmt = $this->conn->prepare("
            INSERT INTO library_borrows (student_id, book_id, borrow_date, due_date, status)
            VALUES (?, ?, CURDATE(), ?, 'Borrowed')
        ");
        $stmt->bind_param('iis', $studentId, $bookId, $dueDate);
        return $stmt->execute();
    }

    public function returnBook(int $borrowId, float $fineAmount = 0): bool
    {
        // Lấy book_id
        $stmt = $this->conn->prepare("SELECT book_id FROM library_borrows WHERE borrow_id = ?");
        $stmt->bind_param('i', $borrowId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if (!$row) return false;

        $bookId = (int)$row['book_id'];

        // Cập nhật borrow
        $stmt = $this->conn->prepare("
            UPDATE library_borrows
            SET status='Returned', return_date=CURDATE(), fine_amount=?
            WHERE borrow_id = ?
        ");
        $stmt->bind_param('di', $fineAmount, $borrowId);
        if (!$stmt->execute()) return false;

        // Tăng available_copies
        $stmt = $this->conn->prepare("
            UPDATE library_books SET available_copies = available_copies + 1
            WHERE book_id = ?
        ");
        $stmt->bind_param('i', $bookId);
        return $stmt->execute();
    }

    public function markLost(int $borrowId): bool
    {
        $stmt = $this->conn->prepare("UPDATE library_borrows SET status='Lost' WHERE borrow_id=?");
        $stmt->bind_param('i', $borrowId);
        return $stmt->execute();
    }

    public function updateOverdueStatuses(): void
    {
        $this->conn->query("
            UPDATE library_borrows
            SET status = 'Overdue'
            WHERE status = 'Borrowed' AND due_date < CURDATE()
        ");
    }

    // ══════════════════════════════════════════════════════
    // MEMBERS (Students)
    // ══════════════════════════════════════════════════════

    public function getActiveMembers(): array
    {
        $result = $this->conn->query("
            SELECT s.student_id, s.student_code,
                   CONCAT(s.first_name,' ',s.last_name) AS full_name,
                   s.email,
                   f.faculty_name,
                   COUNT(CASE WHEN lb.status IN ('Borrowed','Overdue') THEN 1 END) AS active_borrows,
                   COUNT(CASE WHEN lb.status = 'Overdue' THEN 1 END) AS overdue_borrows,
                   SUM(CASE WHEN lb.status IN ('Borrowed','Overdue','Returned') THEN 1 ELSE 0 END) AS total_borrows
            FROM students s
            LEFT JOIN faculties f ON f.faculty_id = s.faculty_id
            LEFT JOIN library_borrows lb ON lb.student_id = s.student_id
            GROUP BY s.student_id
            HAVING total_borrows > 0
            ORDER BY active_borrows DESC, s.student_code
        ");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function searchStudents(string $keyword): array
    {
        $like = '%' . $keyword . '%';
        $stmt = $this->conn->prepare("
            SELECT s.student_id, s.student_code,
                   CONCAT(s.first_name,' ',s.last_name) AS full_name, s.email
            FROM students s
            WHERE s.student_code LIKE ? OR CONCAT(s.first_name,' ',s.last_name) LIKE ? OR s.email LIKE ?
            ORDER BY s.student_code LIMIT 30
        ");
        $stmt->bind_param('sss', $like, $like, $like);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getMemberBorrowHistory(int $studentId): array
    {
        $stmt = $this->conn->prepare("
            SELECT lb.*, b.title, b.author
            FROM library_borrows lb
            JOIN library_books b ON b.book_id = lb.book_id
            WHERE lb.student_id = ?
            ORDER BY lb.borrow_date DESC
        ");
        $stmt->bind_param('i', $studentId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // ══════════════════════════════════════════════════════
    // REPORTS
    // ══════════════════════════════════════════════════════

    public function getTopBorrowedBooks(int $limit = 10): array
    {
        $stmt = $this->conn->prepare("
            SELECT b.book_id, b.title, b.author, b.category,
                   COUNT(lb.borrow_id) AS borrow_count,
                   SUM(CASE WHEN lb.status IN ('Borrowed','Overdue') THEN 1 ELSE 0 END) AS current_borrows
            FROM library_books b
            LEFT JOIN library_borrows lb ON lb.book_id = b.book_id
            GROUP BY b.book_id
            ORDER BY borrow_count DESC
            LIMIT ?
        ");
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getMonthlyStats(): array
    {
        $result = $this->conn->query("
            SELECT DATE_FORMAT(borrow_date, '%Y-%m') AS month,
                   COUNT(*) AS borrows,
                   SUM(CASE WHEN status='Returned' THEN 1 ELSE 0 END) AS returns,
                   SUM(fine_amount) AS fines
            FROM library_borrows
            WHERE borrow_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
            GROUP BY DATE_FORMAT(borrow_date, '%Y-%m')
            ORDER BY month ASC
        ");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getCategoryStats(): array
    {
        $result = $this->conn->query("
            SELECT b.category,
                   COUNT(DISTINCT b.book_id) AS book_count,
                   SUM(b.total_copies) AS total_copies,
                   SUM(b.available_copies) AS available_copies,
                   COUNT(lb.borrow_id) AS borrow_count
            FROM library_books b
            LEFT JOIN library_borrows lb ON lb.book_id = b.book_id
            GROUP BY b.category
            ORDER BY borrow_count DESC
        ");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getTotalFines(): float
    {
        $res = $this->conn->query("SELECT SUM(fine_amount) AS total FROM library_borrows WHERE status = 'Returned'");
        return (float)($res->fetch_assoc()['total'] ?? 0);
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
}
