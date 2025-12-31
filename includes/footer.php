</div><!-- /.main-content -->
</div><!-- /.content-wrapper -->

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>

<script>
    // Sidebar Toggle
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarClose = document.getElementById('sidebarClose');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    
    // Open sidebar
    sidebarToggle.addEventListener('click', function () {
        sidebar.classList.toggle('toggled');
        // On mobile, also toggle overlay
        if (window.innerWidth <= 768) {
            sidebarOverlay.classList.toggle('active');
        }
    });
    
    // Close sidebar (mobile only)
    if (sidebarClose) {
        sidebarClose.addEventListener('click', function () {
            sidebar.classList.remove('toggled');
            sidebarOverlay.classList.remove('active');
        });
    }
    
    // Close sidebar when clicking overlay
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function () {
            sidebar.classList.remove('toggled');
            sidebarOverlay.classList.remove('active');
        });
    }
    
    // Handle resize - reset sidebar state when switching between mobile/desktop
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            sidebarOverlay.classList.remove('active');
        }
    });

    // DataTables initialization
    $(document).ready(function () {
        if ($('#dataTable').length) {
            $('#dataTable').DataTable({
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    },
                    emptyTable: "Tidak ada data",
                    zeroRecords: "Tidak ditemukan data yang cocok"
                }
            });
        }
    });

    // Delete confirmation
    function confirmDelete(form) {
        if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
            form.submit();
        }
        return false;
    }
</script>
</body>

</html>