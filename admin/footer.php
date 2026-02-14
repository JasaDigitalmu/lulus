    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('sidebarToggle').addEventListener('click', function() {
        var sidebar = document.getElementById('sidebar');
        if (window.innerWidth <= 768) {
            sidebar.classList.toggle('show');
        } else {
            sidebar.classList.toggle('collapsed');
        }
    });

    // Close Dropdown in Subject Form from closing when clicking inside
    document.querySelectorAll('.dropdown-menu-prevent-close').forEach(function(element){
        element.addEventListener('click', function (e) {
          e.stopPropagation();
        });
    });
</script>
</body>
</html>
