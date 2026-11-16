<?php wp_footer(); ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const slides = document.querySelectorAll(".slide-image");
        if (slides.length < 2) {
            return;
        }
        const title = document.getElementById("slide-title");
        const client = document.getElementById("slide-client");
        const date = document.getElementById("slide-date");
        if (!title || !client || !date) {
            return;
        }

        let index = 0;
        const total = slides.length;

        setInterval(() => {
            slides[index].style.display = "none";
            index = (index + 1) % total;
            slides[index].style.display = "block";

            title.textContent = slides[index].dataset.title;
            client.textContent = slides[index].dataset.client;
            date.textContent = slides[index].dataset.date;
        }, 2000);
    });
</script>
</body>

</html>