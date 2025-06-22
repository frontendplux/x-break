import { prevent_a_from_click } from "./prevents_href.js";
export default async function router(path){
   const currentlocation=localStorage.getItem('path');
   if(currentlocation === path){
       history.replaceState({path:path},"", path);
   }
   else{
    history.pushState({path:path},"",path);
    localStorage.setItem('path',path);
   }
   document.getElementById('root').innerHTML= await loadurl(path);
   prevent_a_from_click();
}

function loadurl(path){
   switch (path) {
    case '/':
        return  home();
        break;

    case '/login':
        return login();
        break;
   
    default:
        break;
   }
}