<?php
/**
 * ScheduleController - Lịch học sinh viên
 *
 * Permission flow:
 *   [L1] Router: RBACMiddleware::check → 'schedule.view'
 *   [L2] Controller: $this->requirePermission('schedule.view')
 */
class ScheduleController extends BaseStudentController
{
    private array $periodTimes = [
        1  => ['07:00', '07:50'],
        2  => ['07:55', '08:45'],
        3  => ['08:50', '09:40'],
        4  => ['09:50', '10:40'],
        5  => ['10:45', '11:35'],
        6  => ['13:00', '13:50'],
        7  => ['13:55', '14:45'],
        8  => ['14:50', '15:40'],
        9  => ['15:55', '16:45'],
        10 => ['16:50', '17:40'],
    ];

    private array $dayNames = [
        2 => 'Thứ Hai', 3 => 'Thứ Ba', 4 => 'Thứ Tư',
        5 => 'Thứ Năm', 6 => 'Thứ Sáu', 7 => 'Thứ Bảy',
    ];

    private array $palette = [
        '#4e73df','#1cc88a','#36b9cc','#f6c23e',
        '#e74a3b','#858796','#5a5c69','#6f42c1',
        '#fd7e14','#17a2b8','#28a745','#dc3545',
    ];

    public function index(): void
    {
        // [LAYER 2] Controller permission check
        $this->requirePermission('schedule.view');

        $semesterOptions = $this->model->getEnrolledSemesters($this->studentId);

        $filterSemester = $_GET['semester'] ?? '';
        $filterYear     = (int)($_GET['year'] ?? 0);

        if (!$filterSemester && !empty($semesterOptions)) {
            $filterSemester = $semesterOptions[0]['semester'];
            $filterYear     = (int)$semesterOptions[0]['year'];
        }

        $rows = $this->model->getSchedule(
            $this->studentId,
            $filterSemester ?: null,
            $filterYear     ?: null
        );

        [$grid, $colorMap, $classes] = $this->buildTimetableGrid($rows);

        $hasSchedule = !empty(array_filter($grid));

        $this->render('schedule/index.php', [
            'pageTitle'       => 'Lịch học',
            'semesterOptions' => $semesterOptions,
            'filterSemester'  => $filterSemester,
            'filterYear'      => $filterYear,
            'rows'            => $rows,
            'grid'            => $grid,
            'colorMap'        => $colorMap,
            'classes'         => $classes,
            'hasSchedule'     => $hasSchedule,
            'periodTimes'     => $this->periodTimes,
            'dayNames'        => $this->dayNames,
        ]);
    }

    private function buildTimetableGrid(array $rows): array
    {
        $grid     = [];
        $colorMap = [];
        $colorIdx = 0;
        $classes  = [];

        foreach ($rows as $row) {
            $cid = $row['class_id'];

            if (!isset($colorMap[$cid])) {
                $colorMap[$cid] = $this->palette[$colorIdx % count($this->palette)];
                $colorIdx++;
                $classes[$cid] = $row;
            }

            if ($row['schedule_id'] === null) continue;

            $day   = (int)$row['day_of_week'];
            $start = (int)$row['start_period'];
            $end   = (int)$row['end_period'];
            $span  = $end - $start + 1;

            if (isset($grid[$day][$start])) continue;

            $grid[$day][$start] = ['type' => 'start', 'span' => $span, 'data' => $row];
            for ($p = $start + 1; $p <= $end; $p++) {
                $grid[$day][$p] = ['type' => 'occupied'];
            }
        }

        return [$grid, $colorMap, $classes];
    }
}
