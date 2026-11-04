import fetch from '~/utils/fetch';

const CARD_STATUS = {
  STATUS_NORMAL: 0,
  STATUS_SOLD: 1,
  STATUS_USED: 2
};

const CARD_TYPE = {
  TYPE_ONETIME: 0,
  TYPE_REPEAT: 1,
};
export { CARD_STATUS, CARD_TYPE }


export function getList (params) {
  return fetch({
    url: 'card',
    method: 'get',
    params
  });
}

export function add (data) {
  return fetch({
    url: 'card/add',
    method: 'post',
    data
  });
}


export function edit (data) {
  return fetch({
    url: 'card/edit',
    method: 'post',
    data
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

export function trashRestoreAll (data) {
  return fetch({
    url: 'card/trash/restore/all',
    method: 'post',
    data
  });
}

export function trashDelete (ids) {
  return fetch({
    url: 'card/trash/delete',
    method: 'post',
    data: { ids }
  });
}

export function deleteAll (data) {
  return fetch({
    url: 'card/delete/all',
    method: 'post',
    data
  });
}
