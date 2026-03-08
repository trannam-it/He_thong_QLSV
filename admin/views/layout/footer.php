<?php
// admin/views/layout/footer.php
?>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        // Toast notification
        function showToast(message, type = 'success') {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const html = `
                <div class="alert alert-dismissible fade show ${alertClass}" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            const container = document.querySelector('.main-content');
            const alert = document.createElement('div');
            alert.innerHTML = html;
            container.insertBefore(alert.firstElementChild, container.firstChild);

            setTimeout(() => {
                document.querySelector('.alert')?.remove();
            }, 5000);
        }

        // API Helper
        async function apiCall(module, action, method = 'GET', data = null) {
            let url = `/web_QLSV/admin/api/router.php?module=${module}&action=${action}`;
            let options = {
                method: method,
                headers: {
                    'Content-Type': 'application/json'
                }
            };

            if (method === 'POST' || method === 'PUT') {
                options.body = JSON.stringify(data);
            } else if (method === 'GET' && data) {
                const params = new URLSearchParams(data);
                url += '&' + params.toString();
            }

            try {
                const response = await fetch(url, options);
                return await response.json();
            } catch (error) {
                console.error('API Error:', error);
                return { success: false, message: 'API Error' };
            }
        }

        // Delete confirmation
        async function confirmDelete(module, id, name) {
            if (confirm(`Bạn chắc chắn muốn xóa "${name}"?`)) {
                const result = await apiCall(module, 'delete', 'POST', { id: id });
                if (result.success) {
                    showToast('Xóa thành công!', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(result.message, 'error');
                }
            }
        }

        // ─── Global modal backdrop cleanup ──────────────────────────────
        // Tự động dọn backdrop/overflow sau mỗi lần modal đóng,
        // tránh màn hình xám khi đóng modal bằng JS (programmatic hide).
        document.addEventListener('hidden.bs.modal', function () {
            // Nếu không còn modal nào đang mở thì cleanup hoàn toàn
            if (!document.querySelector('.modal.show')) {
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('overflow');
                document.body.style.removeProperty('padding-right');
            }
        });

        // Helper toàn cục: đóng modal và gọi callback sau khi backdrop đã mất
        window.closeModal = function (modalId, callback) {
            const el = document.getElementById(modalId);
            if (!el) { if (callback) callback(); return; }
            const instance = bootstrap.Modal.getInstance(el);
            if (!instance) {
                document.querySelectorAll('.modal-backdrop').forEach(e => e.remove());
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('overflow');
                document.body.style.removeProperty('padding-right');
                if (callback) callback();
                return;
            }
            el.addEventListener('hidden.bs.modal', function handler() {
                el.removeEventListener('hidden.bs.modal', handler);
                if (callback) callback();
            });
            instance.hide();
        };
        // ────────────────────────────────────────────────────────────────
    </script>
</body>
</html>