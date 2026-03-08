<?php
class LibrarianMembersController extends BaseLibrarianController
{
    public function index(): void
    {
        $members = $this->model->getActiveMembers();

        $memberDetail  = null;
        $memberHistory = [];
        if (!empty($_GET['detail'])) {
            $sid           = (int)$_GET['detail'];
            $memberHistory = $this->model->getMemberBorrowHistory($sid);
            // find member in list
            foreach ($members as $m) {
                if ((int)$m['student_id'] === $sid) {
                    $memberDetail = $m;
                    break;
                }
            }
        }

        $this->render('members/index.php', [
            'members'       => $members,
            'memberDetail'  => $memberDetail,
            'memberHistory' => $memberHistory,
            'success'       => $this->getFlash('success'),
            'error'         => $this->getFlash('error'),
        ]);
    }
}
