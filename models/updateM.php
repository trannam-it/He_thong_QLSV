<!-- Modal
 <form action="../public/update.php" method="POST">
<div
    class="modal fade"
    id="edit<?= $row['id']; ?>"
    tabindex="-1"
    role="dialog"
    aria-labelledby="modalTitleId"
    aria-hidden="true"
>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">
                    Update data
                </h5>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>
            </div>
            <div class="modal-body">
                <!-- <div class="container-fluid">Add rows here</div> -->
                 <!-- <div class="col-md-12 mb-2">
                    <label for="" class="form-label">Name</label>
                    <input type="hidden" name="id" id="id" value="<?php echo $row['id']; ?>">
                    <input type="text" class="form-control" id="" name="name" value="<?php echo $row['name']; ?>" required />
                 </div>

                 <div class="col-md-12 mb-2">
                    <label for="" class="form-label">Email</label>
                    <input type="text" class="form-control" id="email" name="email" value="<?php echo $row['email']; ?>" required />
                 </div>

                 <div class="col-md-12 mb-2">
                    <label for="" class="form-label">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $row['phone']; ?>" required />
                 </div>
                 
                 
                 
            </div>
            <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal"
                >
                    Close
                </button>
                <button type="submit" class="btn btn-primary">Save</button>
        
            </div>
        </div>
    </div>
</div>
</form>  -->

<!-- <script>
    var modalId = document.getElementById('modalId');

    modalId.addEventListener('show.bs.modal', function (event) {
          // Button that triggered the modal
          let button = event.relatedTarget;
          // Extract info from data-bs-* attributes
          let recipient = button.getAttribute('data-bs-whatever');

        // Use above variables to manipulate the DOM
    }); -->
</script>

// thêm mới GHI LOG KHI CẬP NHẬT SINH VIÊN

<?php
session_start();
require_once __DIR__ . '/../Database/db.php';
require_once __DIR__ . '/../includes/audit_log.php';

$id = (int)$_POST['id'];

/* OLD */
$stmt = $conn->prepare("SELECT * FROM students WHERE student_id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$old = $stmt->get_result()->fetch_assoc();

/* NEW DATA */
$first_name = $_POST['first_name'];
$last_name  = $_POST['last_name'];
$email      = $_POST['email'];
$phone      = $_POST['phone'];

$stmt = $conn->prepare("
    UPDATE students 
    SET first_name=?, last_name=?, email=?, phone=?
    WHERE student_id=?
");
$stmt->bind_param("ssssi", $first_name, $last_name, $email, $phone, $id);
$stmt->execute();

/* NEW */
$stmt = $conn->prepare("SELECT * FROM students WHERE student_id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$new = $stmt->get_result()->fetch_assoc();

/* AUDIT */
writeAuditLog(
    $conn,
    $_SESSION['user_id'],
    $_SESSION['username'],
    'UPDATE',
    'students',
    $id,
    $old,
    $new
);

header("Location: ../public/home.php");
exit;
