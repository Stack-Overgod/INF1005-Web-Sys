  // Close other open FAQ items when one is clicked
  const details = document.querySelectorAll('.faq-item');
  details.forEach((targetDetail) => {
    targetDetail.addEventListener('click', () => {
      details.forEach((detail) => {
        if (detail !== targetDetail) {
          detail.removeAttribute('open');
        }
      });
    });
  });