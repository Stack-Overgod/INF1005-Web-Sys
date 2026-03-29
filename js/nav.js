console.log('nav.js loaded');

// Mobile nav toggle
const toggle = document.getElementById('navToggle');
const links = document.getElementById('navLinks');
if (toggle && links) {
  toggle.addEventListener('click', () => {
    const open = links.classList.toggle('open');
    toggle.setAttribute('aria-expanded', open);
  });
}

const mobileSearchBtn = document.getElementById('mobileSearchBtn');
const searchBtn = document.getElementById('searchBtn');
const searchBar = document.getElementById('searchBar');
const mobileSearchBack = document.getElementById('mobileSearchBack');
const mobileSearchNav = document.getElementById('mobileSearchNav');
const navbar = document.querySelector('.navbar');

mobileSearchBtn.addEventListener('click', () => {
  navbar.classList.add('search-active');
  mobileSearchNav.querySelector('input').focus();
  console.log('HI IM MOBILE');
});

mobileSearchBack.addEventListener('click', () => {
  navbar.classList.remove('search-active');
});

searchBtn.addEventListener('click', () => {
  const isMobile = window.innerWidth <= 768;
  console.log('isMobile:', isMobile, 'width:', window.innerWidth);
  
  if (isMobile) {
    searchBar.classList.toggle('mobile-active');
    links.classList.toggle('search-open');
    if (searchBar.classList.contains('mobile-active')) {
      searchBar.querySelector('.search-input').focus();
    }
  } else {
    document.getElementById('searchForm').submit();
  }
});

// close on click outside
document.addEventListener('click', (e) => {
  if (!searchBar.contains(e.target) && !searchBtn.contains(e.target)) {
    searchBar.classList.remove('mobile-active');
    links.classList.remove('search-open');
  }
});