export function prevent_a_from_click(){
  document.querySelectorAll('a').forEach((element) => {
      element.addEventListener('click', (e) => {
          e.preventDefault();
          const href = element.getAttribute('href');
          router(href);
      });
  });
}