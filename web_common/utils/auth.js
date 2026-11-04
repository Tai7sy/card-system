export function getToken() {
  if (window['config'] && window['config']["admin_free_token"]) {
    return window['config']["admin_free_token"];
  }
  return localStorage.getItem("admin_free_token");
}
export function setToken(token, remeber = true) {
  if (window['config']) {
    window['config']["admin_free_token"] = token;
  }
  if (remeber) {
    localStorage.setItem("admin_free_token", token);
  }
}
export function removeToken() {
  if (window['config']) {
    delete window['config']["admin_free_token"];
  }
  localStorage.removeItem("admin_free_token");
}
