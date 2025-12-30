</div><!-- /.main-content -->
</div><!-- /.content-wrapper -->

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>

<script>
    // Sidebar Toggle
    document.getElementById('sidebarToggle').addEventListener('click', function () {
        document.getElementById('sidebar').classList.toggle('toggled');
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