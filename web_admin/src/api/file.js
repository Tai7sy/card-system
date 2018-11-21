import fetch from '../utils/fetch';


export function getList (params) {
  return fetch({
    url: '/admin/file/get',
    method: 'post',
    data: params
  });
}

export function upload () {
  // upload file  return 500Error or {id:fileId}
  return false;
}

export function deleteAction (id) {
  return fetch({
    url: '/admin/file/delete',
    method: 'post',
    data: { id }
  });
}
