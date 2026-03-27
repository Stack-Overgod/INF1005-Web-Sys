// Mobile nav toggle
const toggle = document.getElementById('navToggle');
const links  = document.getElementById('navLinks');
if (toggle && links) {
toggle.addEventListener('click', () => {
    const open = links.classList.toggle('open');
    toggle.setAttribute('aria-expanded', open);
});
}

// const searchBtn = document.getElementById('searchBtn');
// searchBtn.addEventListener('click', searchActive);
// const searchBar = document.getElementById('searchBar');

// function searchActive()
// {
//     links.style.display = "none";
//     searchBtn.style.display = "none";
//     searchBar.style.display = "flex";
// }

// function searchInactive()
// {
//     links.style.display = "block";

// }