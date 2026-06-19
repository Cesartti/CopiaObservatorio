<?php /* Pie de página de contacto unificado (mismo bloque que las páginas modernas). */ ?>
<?php require __DIR__ . '/site-footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- ...otros scripts... -->
<script src="assets/js/indic-genero.js"></script>

<?php
$directoryURI = $_SERVER['REQUEST_URI'];
$path = parse_url($directoryURI, PHP_URL_PATH);
$components = explode('/', $path);
$current_url = trim(end($components), ".php");
if ($current_url === '' || 'index') {
    echo '<script src="assets/js/owl.carousel.min.js"></script>';
    echo '<script src="assets/js/home.js"></script>';
}
?>

<script>
    $(document).ready(() => {
        const navbar = document.getElementById('navbar');
        const mainBody = document.getElementById('main-body');
        const button = document.getElementById('nav-icon');
        const sideMenu = document.getElementById('sidebar-container');
        const documentBody = document.getElementsByTagName('body')[0];;

        let showMenu = false;

        if (!!button) {
            button.addEventListener('click', () => {
                toggleSideMenu();
            });
        }

        function toggleSideMenu() {
            showMenu = !showMenu;
            console.log(showMenu);

            if (showMenu) {
                button.classList.add('open');
                sideMenu.classList.add('opened');
                documentBody.classList.add("opened");
            } else {
                if (button.classList.contains('open')) button.classList.remove('open');
                if (sideMenu.classList.contains('opened')) sideMenu.classList.remove('opened');
                if (documentBody.classList.contains('opened')) documentBody.classList.remove('opened');
            }
        }

        const onScroll = () => {
            const scroll = document.documentElement.scrollTop

            if (scroll > 0) {
                if (navbar) navbar.classList.add("scrolled");
                if (mainBody) mainBody.classList.add("scrolled");
            } else {
                if (navbar) navbar.classList.remove("scrolled")
                if (mainBody) mainBody.classList.remove("scrolled")
            }
        }

        // Use the function
        window.addEventListener('scroll', onScroll);

        const forms = document.getElementsByClassName('needs-validation');
        // Loop over them and prevent submission
        const validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    });
</script>
<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php require __DIR__ . '/assistant-widget.php'; ?>
<?php /* accessibility-widget ya se incluye dentro de site-footer.php */ ?>

</body>

</html>