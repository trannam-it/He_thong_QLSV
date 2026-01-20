<!-- <form action="../public/delete.php" method="POST">
<div
    class="modal fade"
    id="delete<?= $row['id'] ?>"
    tabindex="-1"
    role="dialog"
    aria-labelledby="modalTitleId"
    aria-hidden="true"
> -->
    <!-- <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">
                    Delete Form
                </h5>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">Are you sure to Delete this Record?</div>
            </div>
            <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal"
                >
                    Close
                </button>
                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                <button type="submit" class="btn btn-danger">Delete</button>
            </div>
        </div>
    </div>
</div>
</form> -->

<script>
    // var modalId = document.getElementById('modalId');

    // modalId.addEventListener('show.bs.modal', function (event) {
    //       // Button that triggered the modal
    //       let button = event.relatedTarget;
    //       // Extract info from data-bs-* attributes
    //       let recipient = button.getAttribute('data-bs-whatever');

    //     // Use above variables to manipulate the DOM
    // });
</script>


//  thêm mới GHI LOG KHI XÓA SINH VIÊN
<?php
session_start();
require_once __DIR__ . '/../Database/db.php';
require_once __DIR__ . '/../includes/audit_log.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../public/home.php");
    exit;
}

$id = $_POST['id'];

// OLD DATA
$old = $conn->query(
    "SELECT * FROM students WHERE id = $id"
)->fetch_assoc();

// DELETE
$conn->query("DELETE FROM students WHERE id = $id");

// AUDIT LOG
writeAuditLog(
    $conn,
    $_SESSION['user_id'],
    $_SESSION['username'],
    'DELETE',
    'students',
    $id,
    json_encode($old),
    null
);

header("Location: ../public/home.php");
exit;

