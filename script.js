import router from "./public/waget/router.js";
import { prevent_a_from_click } from "./public/waget/prevents_href.js";
window.addEventListener('DOMContentLoaded', () => {
  router(window.location.pathname);
});
window.onpopstate = (e) => {
    router(e.state.url);
  };
prevent_a_from_click();