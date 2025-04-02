</div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Proyecto UISIL 2025</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="login.html">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="<?=$base_url?>vendor/jquery/jquery.min.js"></script>
    <script src="<?=$base_url?>vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="<?=$base_url?>vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="<?=$base_url?>js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="<?=$base_url?>vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="<?=$base_url?>js/demo/chart-area-demo.js"></script>
    <script src="<?=$base_url?>js/demo/chart-pie-demo.js"></script>

    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog" id="modalDialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div id="modalBody" class="modal-body">
                
            </div>
            </div>
        </div>
    </div>
    <script>
    function cargarModal(action, title, param='', size=''){
        if (size == 'sm'){
            $('#modalDialog').removeClass();
            $('#modalDialog').addClass('modal-dialog');
            $('#modalDialog').addClass('modal-sm');
        }else if (size == 'md'){
            $('#modalDialog').removeClass();
            $('#modalDialog').addClass('modal-dialog');
            $('#modalDialog').addClass('modal-md');
        }else if (size == 'lg'){
            $('#modalDialog').removeClass();
            $('#modalDialog').addClass('modal-dialog');
            $('#modalDialog').addClass('modal-lg');
        }else if (size == 'xl'){
            $('#modalDialog').removeClass();
            $('#modalDialog').addClass('modal-dialog');
            $('#modalDialog').addClass('modal-xl');
        }else{
            $('#modalDialog').removeClass();
            $('#modalDialog').addClass('modal-dialog');
        }

        $.post("<?=$_SERVER['PHP_SELF']?>", {action: action, param: param})
        .done(function(data){
            $('#modalBody').html(data);
            $('#modalTitle').html(title);
            $('#modal').modal('show');
        });
    }
    </script>

    
<script src="../../vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="../../vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="../../js/demo/datatables-demo.js"></script>
</body>
