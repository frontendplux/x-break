import { fetchDataPost } from "./fetch-req.js";
export default async function memberAuth(){
 const  user={  action:'profiles',
                user:localStorage.getItem('user'),
                _id:sessionStorage.getItem('_id')
            }
  const req=await fetchDataPost('/backend/profiles', user);
  return req;
}

export default async function userAuthSession(){
 const  user={  action:'userSession',
                user:localStorage.getItem('user'),
            }
  const req=await fetchDataPost('/backend/profiles', user);
  return req;
}
