<?php
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../includes/auth_check.php';
authCheck(['super_admin', 'content_admin']);
require_once __DIR__ . '/../layout/header.php';
?>

	<div class="container-fluid mt-4">

		<!-- PAGE HEADER -->
		<div class="d-flex justify-content-between align-items-center mb-3">
			<h4 class="mb-0">
				<i class="bi bi-building me-2"></i>Quản lý Khoa
			</h4>

			<div class="d-flex gap-2">
				<a href="stats.php" class="btn btn-outline-secondary btn-sm">
					<i class="bi bi-bar-chart"></i> Thống kê
				</a>
				<button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createModal">
					<i class="bi bi-plus-circle"></i> Thêm khoa
				</button>
			</div>
		</div>

		<!-- CARD TABLE -->
		<div class="card shadow-sm">
			<div class="card-body">

				<div id="alertArea"></div>

				<table class="table table-hover table-bordered align-middle" id="facultiesTable">
					<thead class="table-light">
						<tr>
							<th style="width:60px">STT</th>
							<th>Mã khoa</th>
							<th>Tên khoa</th>
							<th>Mô tả</th>
							<th style="width:220px" class="text-center">Hành động</th>
						</tr>
					</thead>
					<tbody id="facultiesBody">
						<!-- populated by JS -->
					</tbody>
				</table>

			</div>
		</div>

	</div>


		<!-- Create Modal -->
		<div class="modal fade" id="createModal" tabindex="-1">
			<div class="modal-dialog">
				<div class="modal-content shadow">
					<div class="modal-header">
						<h5 class="modal-title">
							<i class="bi bi-plus-circle me-2"></i>Thêm khoa
						</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
					</div>

					<form id="createForm">
						<div class="modal-body">
							<div class="mb-3">
								<label class="form-label">Mã khoa</label>
								<input name="faculty_code" id="create_code" class="form-control" required>
							</div>

							<div class="mb-3">
								<label class="form-label">Tên khoa</label>
								<input name="faculty_name" id="create_name" class="form-control" required>
							</div>

							<div class="mb-3">
								<label class="form-label">Mô tả</label>
								<textarea name="description" id="create_desc" class="form-control" rows="3"></textarea>
							</div>
						</div>

						<div class="modal-footer">
							<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
							<button class="btn btn-primary">Tạo</button>
						</div>
					</form>
				</div>
			</div>
		</div>


		<!-- Edit Modal (single) -->
		<div class="modal fade" id="editModal" tabindex="-1">
			<div class="modal-dialog">
				<div class="modal-content shadow">
					<div class="modal-header">
						<h5 class="modal-title">
							<i class="bi bi-pencil-square me-2"></i>Sửa thông tin khoa
						</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
					</div>

					<form id="editForm">
						<div class="modal-body">
							<input type="hidden" id="edit_id">

							<div class="mb-3">
								<label class="form-label">Mã khoa</label>
								<input id="edit_code" class="form-control" required>
							</div>

							<div class="mb-3">
								<label class="form-label">Tên khoa</label>
								<input id="edit_name" class="form-control" required>
							</div>

							<div class="mb-3">
								<label class="form-label">Mô tả</label>
								<textarea id="edit_desc" class="form-control" rows="3"></textarea>
							</div>
						</div>

						<div class="modal-footer">
							<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
							<button class="btn btn-primary">Lưu</button>
						</div>
					</form>
				</div>
			</div>
		</div>



		<!-- Assign Lecturers Modal -->
		<div class="modal fade" id="assignLecturersModal" tabindex="-1">
			<div class="modal-dialog modal-lg">
				<div class="modal-content shadow">
					<div class="modal-header">
						<h5 class="modal-title">
							<i class="bi bi-person-badge me-2"></i>Gán giảng viên
						</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
					</div>

					<form id="assignLecturersForm">
						<div class="modal-body">
							<input type="hidden" id="assign_faculty_id_lec">
							<div class="row g-2" id="lecturersList"></div>
						</div>

						<div class="modal-footer">
							<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
							<button type="submit" class="btn btn-primary"> <i class="bi bi-save"></i>Gán</button> 
						</div>
					</form>
				</div>
			</div>
		</div>


		<!-- Assign Students Modal -->
		<div class="modal fade" id="assignStudentsModal" tabindex="-1">
			<div class="modal-dialog modal-lg ">
				<div class="modal-content shadow">
					<div class="modal-header">
						<h5 class="modal-title"><i class="bi bi-people me-2"></i>Gán sinh viên</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
					</div>
						<form id="assignStudentsForm">
							<div class="modal-body">
								<input type="hidden" id="assign_faculty_id_stu">

								<div class="d-flex justify-content-between align-items-center mb-3">
									<div class="text-muted">
										<i class="bi bi-info-circle"></i>
										Chọn các sinh viên để gán vào khoa
									</div>

									<div class="d-flex gap-2">
										<button type="button" class="btn btn-outline-secondary btn-sm" id="selectAllStudents">
											<i class="bi bi-check2-square"></i> Chọn tất cả
										</button>
										<button type="button" class="btn btn-outline-secondary btn-sm" id="unselectAllStudents">
											<i class="bi bi-square"></i> Bỏ chọn
										</button>
								</div>
                    </div>

							<div class="row g-2" id="studentsList">	
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
							<button type="submit" class="btn btn-primary"> <i class="bi bi-save"></i> Gán</button>
						</div>
					</form>
				</div>
			</div>
		</div>

</div>

<script>
const apiUrl = '/web_QLSV/admin/api/router.php';
let faculties = [], lecturers = [], students = [];

function showAlert(message, type='success'){
	const area = document.getElementById('alertArea');
	area.innerHTML = `<div class="alert alert-${type} alert-dismissible" role="alert">${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`;
}

async function fetchFaculties(){
	try{
		const res = await fetch(`${apiUrl}?module=faculties&action=index&page=1&limit=500`, { credentials: 'same-origin' });
		if (!res.ok) { const txt = await res.text(); showAlert(`Server error: ${res.status} ${txt}`, 'danger'); return; }
		const c = res.headers.get('content-type') || '';
		if (!c.includes('application/json')) { const txt = await res.text(); showAlert(`Invalid response: ${txt}`, 'danger'); return; }
		const j = await res.json();
		if(j.success){ faculties = j.data; renderFaculties(); }
		else showAlert(j.message||'Lỗi khi tải khoa', 'danger');
	}catch(e){ console.error(e); showAlert('Lỗi mạng', 'danger'); }
}

function renderFaculties(){
	const tbody = document.getElementById('facultiesBody');
	tbody.innerHTML = '';
	faculties.forEach(f => {
		const tr = document.createElement('tr');
		tr.innerHTML = `
			<td>${f.faculty_id}</td>
			<td>${escapeHtml(f.faculty_code)}</td>
			<td>${escapeHtml(f.faculty_name)}</td>
			<td>${escapeHtml(f.description||'')}</td>
			<td>
				<button class="btn btn-sm btn-success" onclick="openEdit(${f.faculty_id})">Sửa</button>
				<button class="btn btn-sm btn-danger" onclick="deleteFaculty(${f.faculty_id})">Xóa</button>
				<button class="btn btn-sm btn-outline-primary" onclick="openAssignLecturers(${f.faculty_id})">Gán GV</button>
				<button class="btn btn-sm btn-outline-primary" onclick="openAssignStudents(${f.faculty_id})">Gán SV</button>
			</td>
		`;
		tbody.appendChild(tr);
	});
}

function escapeHtml(str){ return String(str).replace(/[&<>\"]/g, s=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;' })[s]); }

async function openEdit(id){
	try{
		const res = await fetch(`${apiUrl}?module=faculties&action=show&id=${id}`);
		const j = await res.json();
		if(!j.success){ showAlert(j.message||'Không tìm thấy khoa','danger'); return; }
		const f = j.data;
		document.getElementById('edit_id').value = f.faculty_id;
		document.getElementById('edit_code').value = f.faculty_code;
		document.getElementById('edit_name').value = f.faculty_name;
		document.getElementById('edit_desc').value = f.description || '';
		const modal = new bootstrap.Modal(document.getElementById('editModal'));
		modal.show();
	}catch(e){ showAlert('Lỗi mạng','danger'); }
}

document.getElementById('createForm').addEventListener('submit', async function(e){
	e.preventDefault();
	const params = new URLSearchParams();
	params.append('module','faculties'); params.append('action','store');
	params.append('faculty_code', document.getElementById('create_code').value);
	params.append('faculty_name', document.getElementById('create_name').value);
	params.append('description', document.getElementById('create_desc').value);
	try{
		const res = await fetch(apiUrl, { method: 'POST', body: params, credentials: 'same-origin' });
		if (!res.ok) { const txt = await res.text(); showAlert(`Server error: ${res.status} ${txt}`, 'danger'); return; }
		const c = res.headers.get('content-type') || '';
		if (!c.includes('application/json')) { const txt = await res.text(); showAlert(`Invalid response: ${txt}`, 'danger'); return; }
		const j = await res.json();
		if(j.success){ showAlert('Tạo khoa thành công'); fetchFaculties(); document.getElementById('createForm').reset();
			bootstrap.Modal.getInstance(document.getElementById('createModal')).hide();
		} else showAlert(j.message || 'Lỗi', 'danger');
	}catch(e){ console.error(e); showAlert('Lỗi mạng','danger'); }
});

document.getElementById('editForm').addEventListener('submit', async function(e){
	e.preventDefault();
	const id = document.getElementById('edit_id').value;
	const params = new URLSearchParams();
	params.append('module','faculties'); params.append('action','update');
	params.append('id', id);
	params.append('faculty_code', document.getElementById('edit_code').value);
	params.append('faculty_name', document.getElementById('edit_name').value);
	params.append('description', document.getElementById('edit_desc').value);
	try{
		const res = await fetch(apiUrl, { method: 'POST', body: params, credentials: 'same-origin' });
		if (!res.ok) { const txt = await res.text(); showAlert(`Server error: ${res.status} ${txt}`, 'danger'); return; }
		const c = res.headers.get('content-type') || '';
		if (!c.includes('application/json')) { const txt = await res.text(); showAlert(`Invalid response: ${txt}`, 'danger'); return; }
		const j = await res.json();
		if(j.success){ showAlert('Cập nhật thành công'); fetchFaculties();
			bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
		} else showAlert(j.message || 'Lỗi', 'danger');
	}catch(e){ console.error(e); showAlert('Lỗi mạng','danger'); }
});

async function deleteFaculty(id){
	if(!confirm('Xóa khoa này?')) return;
	const params = new URLSearchParams();
	params.append('module','faculties'); params.append('action','delete'); params.append('id', id);
	try{
		const res = await fetch(apiUrl, { method: 'POST', body: params, credentials: 'same-origin' });
		if (!res.ok) { const txt = await res.text(); showAlert(`Server error: ${res.status} ${txt}`, 'danger'); return; }
		const c = res.headers.get('content-type') || '';
		if (!c.includes('application/json')) { const txt = await res.text(); showAlert(`Invalid response: ${txt}`, 'danger'); return; }
		const j = await res.json();
		if(j.success){ showAlert('Đã xóa'); fetchFaculties(); }
		else showAlert(j.message || 'Lỗi', 'danger');
	}catch(e){ console.error(e); showAlert('Lỗi mạng','danger'); }
}

// Fetch lecturers and students for assign dialogs
async function fetchLecturers(){
	try{
		const res = await fetch(`${apiUrl}?module=lecturers&action=index&page=1&limit=500`, { credentials: 'same-origin' });
		if (!res.ok) { lecturers = []; return; }
		const text = await res.text();
		try {
			const j = JSON.parse(text);
			if (j && j.success) lecturers = j.data; else lecturers = [];
		} catch (parseErr) {
			console.warn('fetchLecturers: invalid JSON response, server returned:', text.slice(0,1000));
			lecturers = [];
		}
	}catch(e){ console.error(e); lecturers = []; }
}
async function fetchStudents(){
	try{
		const res = await fetch(`${apiUrl}?module=students&action=index&page=1&limit=500`, { credentials: 'same-origin' });
		if (!res.ok) { students = []; return; }
		const text = await res.text();
		try {
			const j = JSON.parse(text);
			if (j && j.success) students = j.data; else students = [];
		} catch (parseErr) {
			console.warn('fetchStudents: invalid JSON response, server returned:', text.slice(0,1000));
			students = [];
		}
	}catch(e){ console.error(e); students = []; }
}

function openAssignLecturers(faculty_id){
	document.getElementById('assign_faculty_id_lec').value = faculty_id;
	const container = document.getElementById('lecturersList');
	container.innerHTML = '';
	lecturers.forEach(l => {
		const col = document.createElement('div'); col.className='col-6';
		col.innerHTML = `<div class="form-check"><input class="form-check-input" type="checkbox" name="lecturers[]" value="${l.lecturer_id}" id="lec_${l.lecturer_id}"><label class="form-check-label" for="lec_${l.lecturer_id}">${escapeHtml(l.first_name+' '+l.last_name+' ('+l.lecturer_code+')')}</label></div>`;
		container.appendChild(col);
	});
	const modal = new bootstrap.Modal(document.getElementById('assignLecturersModal'));
	modal.show();
}

function openAssignStudents(faculty_id){
	document.getElementById('assign_faculty_id_stu').value = faculty_id;
	const container = document.getElementById('studentsList');
	container.innerHTML = '';
	students.forEach(s => {
		const col = document.createElement('div'); col.className='col-6';
		col.innerHTML = `<div class="form-check"><input class="form-check-input" type="checkbox" name="students[]" value="${s.student_id}" id="stu_${s.student_id}"><label class="form-check-label" for="stu_${s.student_id}">${escapeHtml(s.first_name+' '+s.last_name+' ('+s.student_code+')')}</label></div>`;
		container.appendChild(col);
	});
	const modal = new bootstrap.Modal(document.getElementById('assignStudentsModal'));
	modal.show();
}

document.getElementById('assignLecturersForm').addEventListener('submit', async function(e){
	e.preventDefault();
	const faculty_id = document.getElementById('assign_faculty_id_lec').value;
	const checked = Array.from(document.querySelectorAll('#lecturersList input[type=checkbox]:checked')).map(i=>i.value);
	if(checked.length===0){ showAlert('Chưa chọn giảng viên','warning'); return; }
	const params = new URLSearchParams();
	params.append('module','faculties'); params.append('action','assignLecturers');
	params.append('faculty_id', faculty_id);
	checked.forEach(id => params.append('lecturers[]', id));
	try{
		const res = await fetch(apiUrl, { method: 'POST', body: params, credentials: 'same-origin' });
		if (!res.ok) { const txt = await res.text(); showAlert(`Server error: ${res.status} ${txt}`, 'danger'); return; }
		const c = res.headers.get('content-type') || '';
		if (!c.includes('application/json')) { const txt = await res.text(); showAlert(`Invalid response: ${txt}`, 'danger'); return; }
		const j = await res.json();
		if(j.success){ showAlert('Gán giảng viên thành công'); fetchFaculties();
			bootstrap.Modal.getInstance(document.getElementById('assignLecturersModal')).hide();
		} else showAlert(j.message||'Lỗi','danger');
	}catch(e){ console.error(e); showAlert('Lỗi mạng','danger'); }
});

document.getElementById('assignStudentsForm').addEventListener('submit', async function(e){
	e.preventDefault();
	const faculty_id = document.getElementById('assign_faculty_id_stu').value;
	const checked = Array.from(document.querySelectorAll('#studentsList input[type=checkbox]:checked')).map(i=>i.value);
	if(checked.length===0){ showAlert('Chưa chọn sinh viên','warning'); return; }
	const params = new URLSearchParams();
	params.append('module','faculties'); params.append('action','assignStudents');
	params.append('faculty_id', faculty_id);
	checked.forEach(id => params.append('students[]', id));
	try{
		const res = await fetch(apiUrl, { method: 'POST', body: params, credentials: 'same-origin' });
		if (!res.ok) { const txt = await res.text(); showAlert(`Server error: ${res.status} ${txt}`, 'danger'); return; }
		const c = res.headers.get('content-type') || '';
		if (!c.includes('application/json')) { const txt = await res.text(); showAlert(`Invalid response: ${txt}`, 'danger'); return; }
		const j = await res.json();
		if(j.success){ showAlert('Gán sinh viên thành công'); fetchFaculties();
			bootstrap.Modal.getInstance(document.getElementById('assignStudentsModal')).hide();
		} else showAlert(j.message||'Lỗi','danger');
	}catch(e){ console.error(e); showAlert('Lỗi mạng','danger'); }
});

// function renderStudents(students) {
//     const list = document.getElementById('studentsList');
//     list.innerHTML = '';

//     students.forEach(stu => {
//         list.insertAdjacentHTML('beforeend', `
//             <div class="col-md-4 col-lg-3">
//                 <label class="card h-100 p-2 student-item">
//                     <div class="d-flex align-items-start gap-2">
//                         <input
//                             type="checkbox"
//                             class="form-check-input mt-1 student-checkbox"
//                             name="student_ids[]"
//                             value="${stu.student_id}"
//                             ${stu.checked ? 'checked' : ''}
//                         >
//                         <div>
//                             <div class="fw-semibold">${stu.full_name}</div>
//                             <div class="text-muted small">MSSV: ${stu.student_code}</div>
//                         </div>
//                     </div>
//                 </label>
//             </div>
//         `);
//     });
// }


document.getElementById('selectAllStudents').onclick = () => {
	document.querySelectorAll('#studentsList input[type=checkbox]')
		.forEach(cb => cb.checked = true);
};

document.getElementById('unselectAllStudents').onclick = () => {
	document.querySelectorAll('#studentsList input[type=checkbox]')
		.forEach(cb => cb.checked = false);
};



// Init
Promise.all([fetchLecturers(), fetchStudents()]).then(()=>fetchFaculties());
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

