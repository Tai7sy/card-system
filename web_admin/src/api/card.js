const fetch = window.$http;

const cardStatus = {
  STATUS_NORMAL: 0,
  STATUS_SOLD: 1,
  STATUS_USED: 2
};
export { cardStatus }

const cardType = {
  TYPE_ONETIME: 0,
  TYPE_REPEAT: 1,
};
export { cardType }


export function getList (params) {
  return fetch({
    url: 'card',
    method: 'post',
    data: params
  });
}

export function exportList (params) {
  return fetch({
    url: 'card/export',
    method: 'post',
    data: params
  });
}

export function add (params) {
  return fetch({
    url: 'card/add',
    method: 'post',
    data: params
  });
}


export function edit (params) {
  return fetch({
    url: 'card/edit',
    method: 'post',
    data: params
  });
}


export function moveToTrash (ids) {
  return fetch({
    url: 'card/delete',
    method: 'post',
    data: { ids }
  });
}


export function trashRestore (ids) {
  return fetch({
    url: 'card/trash/restore',
    method: 'post',
    data: { ids }
  });
}

export function trashRestoreAll (params) {
  return fetch({
    url: 'card/trash/restore/all',
    method: 'post',
    data: params
  });
}

export function trashDelete (ids) {
  return fetch({
    url: 'card/trash/delete',
    method: 'post',
    data: { ids }
  });
}

export function deleteAll (params) {
  return fetch({
    url: 'card/delete/all',
    method: 'post',
    data: params
  });
}
