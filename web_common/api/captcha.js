
export function captcha () {
  return window.$http.get('captcha?t=' + Math.random());
}