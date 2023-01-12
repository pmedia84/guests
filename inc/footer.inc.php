<footer class="footer">
    <p>&copy; Parrot Media </p>
</footer>
<script src="./assets/js/app.js"></script>
<script>
        $(document).ready(function(){
        var pathname = window.location.href;
    $(".nav-links a").each(function(){
        if(this.href === pathname){
            $(this).addClass("link-active");
        }else(
            $(this).removeClass("link-active")
        )
    })
})
</script>