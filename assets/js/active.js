document.addEventListener("DOMContentLoaded", function() {
    let currentPage = window.location.pathname.split("/").pop();
    let menuLinks = document.querySelectorAll("nav ul li a");

    menuLinks.forEach(link => {
        if (link.getAttribute("href") === currentPage) {
            link.classList.add("active");
        } else {
            link.classList.remove("active");
        }
    });
});
