document.getElementById("searchButton").addEventListener("click", function() {
    let searchBox = document.getElementById("search");
    if (searchBox.style.display === "none" || searchBox.style.display === "") {
        searchBox.style.display = "block";
    } else {
        searchBox.style.display = "none";
    }
});
