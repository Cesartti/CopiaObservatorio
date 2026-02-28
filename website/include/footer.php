<footer class="footer py-3 m-0 border-top">
    <div class="container">
		<div class="footer-title">
			Contacto
		</div>
        <div class="footer-text">
            Palacio de la Torre, Calle 20 No. 9 – 90, Secretaría de Planeación - piso 2 <br>
            <a href="mailto:rotpp.planeacion@boyaca.gov.co">rotpp.planeacion@boyaca.gov.co</a><br>
            <a href="tel:57-608-7420150">PBX + (57) 608 7420150</a> 
        </div>
    </div>
</footer>

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


</body>

</html>