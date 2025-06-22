export async function fetchDataPost(url,data){
    const req=await fetch(url,{method:'post', body:JSON.stringify(data)}).then(res => res.json());
    return req;
}

export async function fetchDataGet(url){
    const req=await fetch(url).then(res => res.json());
    return req;
}