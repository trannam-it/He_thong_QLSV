<!-- <form action="../public/insert.php" method="post">
<!-- Modal -->
<!-- <div
    class="modal fade"
    id="mymodal"
    tabindex="-1"
    role="dialog"
    aria-labelledby="modalTitleId"
    aria-hidden="true"
>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">
                    Add Student Form
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
                    <label for="" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="" name="name" placeholder="Enter the Full name" value="" required />
                 </div>

                 <div class="col-md-12 mb-2">
                    <label for="" class="form-label">Email</label>
                    <input type="text" class="form-control" id="" name="email" placeholder="Enter the email" value="" required />
                 </div>

                 <div class="col-md-12 mb-2">
                    <label for="" class="form-label">Phone</label>
                    <input type="text" class="form-control" id="" name="phone" placeholder="Enter the phone" value="" required />
                 </div>
                  -->
                 
                 
            <!-- </div>
            <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal"
                >
                    Close
                </button>
                <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
</div> -->

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


// thêm mới GHI LOG KHI THÊM SINH VIÊN
<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/audit_log.php';

$first_name  = $_POST['first_name'];
$last_name   = $_POST['last_name'];
$email       = $_POST['email'];
$phone       = $_POST['phone'];
$faculty_id  = $_POST['faculty_id'];

$stmt = $conn->prepare("
    INSERT INTO students (first_name, last_name, email, phone, faculty_id)
    VALUES (?, ?, ?, ?, ?)
");
$stmt->bind_param("ssssi", $first_name, $last_name, $email, $phone, $faculty_id);
$stmt->execute();

$student_id = $conn->insert_id;

/* AUDIT */
writeAuditLog(
    $conn,
    $_SESSION['user_id'],
    $_SESSION['username'],
    'INSERT',
    'students',
    $student_id,
    null,
    $_POST
);

header("Location: ../public/home.php");
exit;
