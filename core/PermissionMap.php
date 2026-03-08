<?php
/**
 * PermissionMap - central definition of route→permission mappings.
 *
 * This class holds the correspondence between the three pieces that make up
 * an API endpoint (module/resource/action) and the permission code that must
 * be granted to a user in order to execute that endpoint.  Historically each
 * router defined its own `$permissionMap` array and performed the check
 * inline.  The result was duplicated logic and many overlooked actions.  The
 * new RBAC middleware reads the map once from here and does the check
 * automatically.
 *
 * The map can be built from a database table (`permission_routes`) if one is
 * present, which makes it easy to manage routes from an administrative UI.
 * When the table is missing (most existing installations) the hard‑coded
 * `staticMap()` is used.
 */

class PermissionMap
{
    /**
     * Return the current permission map.  $conn is optional; if provided an
     * attempt is made to load the routes from the database.  A null return
     * from loadFromDb() indicates the table does not exist and the static map
     * will be returned instead.
     *
     * @param mysqli|null $conn
     * @return array
     */
    public static function get(mysqli $conn = null): array
    {
        if ($conn !== null) {
            $fromDb = self::loadFromDb($conn);
            if ($fromDb !== null) {
                return $fromDb;
            }
        }
        return self::staticMap();
    }

    /**
     * Try to load map entries from the `permission_routes` table.
     *
     * Returns null if the table does not exist, otherwise returns an array of
     * the same shape as staticMap().
     *
     * @param mysqli $conn
     * @return array|null
     */
    private static function loadFromDb(mysqli $conn): ?array
    {
        $check = $conn->query("SHOW TABLES LIKE 'permission_routes'");
        if (!$check || $check->num_rows === 0) {
            return null;
        }

        $map = [];
        $stmt = $conn->prepare(
            "SELECT module, resource, action, permission_code FROM permission_routes"
        );
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $map[$row['module']][$row['resource']][$row['action']] = $row['permission_code'];
        }
        return $map;
    }

    /**
     * Hard‑coded fallback map.  Add new entries here when you create a new
     * endpoint, or extend the database table and remove the corresponding
     * code later.
     *
     * @return array
     */
    private static function staticMap(): array
    {
        return [
            'student' => [
                'enrollment' => [
                    'list'           => 'enrollment.view',
                    'available'      => 'enrollment.view',
                    'current_period' => 'enrollment.view',
                    'register'       => 'enrollment.register',
                    'cancel'         => 'enrollment.cancel',
                ],
                'grades' => [
                    'all' => 'grades.view',
                    'gpa' => 'grades.view',
                ],
                'schedule' => [
                    'list'      => 'schedule.view',
                    'semesters' => 'schedule.view',
                ],
                'tuition' => [
                    'invoices' => 'tuition.view',
                    'pay'      => 'tuition.pay',
                ],
                'scholarship' => [
                    'available'       => 'scholarship.view',
                    'my_applications' => 'scholarship.view',
                    'apply'           => 'scholarship.apply',
                    'cancel'          => 'scholarship.cancel',
                ],
                'dormitory' => [
                    'available_rooms'  => 'dormitory.view',
                    'my_registrations' => 'dormitory.view',
                    'register'         => 'dormitory.register',
                    'cancel'           => 'dormitory.cancel',
                ],
                'library' => [
                    'books'   => 'library.view',
                    'history' => 'library.view',
                    'borrow'  => 'library.borrow',
                ],
                'profile' => [
                    'info'           => 'profile.view',
                    'update_contact' => 'profile.edit',
                ],
            ],
            'librarian' => [
                'books' => [
                    'list'   => 'books.view',
                    'create' => 'books.create',
                    'update' => 'books.edit',
                    'delete' => 'books.delete',
                ],
                'borrows' => [
                    'list'      => 'borrows.view',
                    'borrow'    => 'borrows.borrow',
                    'return'    => 'borrows.return',
                    'mark_lost' => 'borrows.mark_lost',
                ],
                'members' => [
                    'list'   => 'members.view',
                    'search' => 'members.search',
                    'history'=> 'members.history',
                ],
                'stats' => [
                    'dashboard' => 'stats.view',
                ],
            ],
            'lecturer' => [
                'grades' => [
                    'list'  => 'grades.view',
                    'stats' => 'grades.view',
                    'save'  => 'grades.edit',
                ],
                'attendance' => [
                    'history'  => 'attendance.view',
                    'by_date'  => 'attendance.view',
                    'save'     => 'attendance.edit',
                    'summary'  => 'attendance.view',
                ],
                'classes' => [
                    'list'  => 'classes.view',
                    'stats' => 'classes.view',
                ],
                'class_registration' => [
                    'available' => 'classes.register',
                    'register'  => 'classes.register',
                ],
                'profile' => [
                    'info'           => 'profile.view',
                    'update_contact' => 'profile.edit',
                ],
            ],
            'accountant' => [
                'tuition' => [
                    'list'          => 'tuition.view',
                    'pay'           => 'tuition.pay',
                    'update_status' => 'tuition.edit',
                    'settings'      => 'tuition.view',
                ],
                'scholarships' => [
                    'list'         => 'scholarships.view',
                    'applications' => 'scholarships.view',
                    'review'       => 'scholarships.edit',
                ],
                'students' => [
                    'list'     => 'students.view',
                    'invoices' => 'students.view',
                ],
                'reports' => [
                    'tuition_by_semester'   => 'reports.view',
                    'scholarship_summary'  => 'reports.view',
                    'dashboard'            => 'reports.view',
                ],
            ],
            'academic' => [
                'students'   => [
                    'list'   => 'students.view',
                    'detail' => 'students.view',
                ],
                'subjects'   => [
                    'list'   => 'subjects.view',
                    'create' => 'subjects.create',
                    'update' => 'subjects.edit',
                    'delete' => 'subjects.delete',
                ],
                'classes'    => [
                    'list'     => 'classes.view',
                    'students' => 'classes.view',
                ],
                'semesters'  => [
                    'list'        => 'semesters.view',
                    'set_current' => 'semesters.edit',
                ],
                'enrollments'=> [
                    'list' => 'enrollments.view',
                ],
               'enrollment_periods' => [
                    'list'   => 'enrollment.manage',
                    'store'  => 'enrollment.manage',
                    'update' => 'enrollment.manage',
                    'toggle_active' => 'enrollment.manage',
                    'delete' => 'enrollment.manage',
                ],
                'grades'     => [
                    'by_class' => 'grades.view',
                ],
                'reports'    => [
                    'semester_grades' => 'reports.view',
                ],
            ],
        ];
    }
}

// end of PermissionMap
