import fetch from '~/utils/fetch';

const ORDER_STATUS = {
  UN_PAY: {
    id: 0,
    name: '未支付',
    type: 'info'
  },
  PAID: {
    id: 1,
    name: '待发货',
    type: 'primary' // 高亮
  },
  SUCCESS: {
    id: 2,
    name: '已成功',
    type: 'success' // 高亮
  },
  FROZEN: {
    id: 3,
    name: '已冻结',
    type: 'danger' // 高亮
  },
  REFUND: {
    id: 4,
    name: '已退款',
    type: 'warning' // 高亮
  },

  get (id) {
    for (const key in this) {
      if (Object.prototype.hasOwnProperty.call(this, key) && this[key].id === id) {
        return this[key]
      }
    }
    return {
      id,
      type: 'danger',
      name: '未知状态'
    };
  }
};

const SEND_STATUS = {
  UN: {
    id: 0,
    name: '',
    type: ''
  },
  EMAIL_SUCCESS: {
    id: 1,
    name: '已发送邮件',
    type: 'info'
  },
  EMAIL_FAILED: {
    id: 2,
    name: '邮件发送失败',
    type: 'warning' // 高亮
  },
  MOBILE_SUCCESS: {
    id: 3,
    name: '已发送短信',
    type: 'success' // 高亮
  },
  MOBILE_FAILED: {
    id: 4,
    name: '短信发送失败',
    type: 'warning' // 高亮
  },


  CARD_UN: {
    id: 100,
    name: '待发货',
    type: 'info'
  },
  CARD_PROCESSING: {
    id: 101,
    name: '正在处理',
    type: 'primary'
  },
  CARD_SUCCESS: {
    id: 102,
    name: '已发货',
    type: 'success'
  },
  CARD_FAILED: {
    id: 103,
    name: '处理失败',
    type: 'danger'
  },
  get (id) {
    for (const key in this) {
      if (Object.prototype.hasOwnProperty.call(this, key) && this[key].id === id) {
        return this[key]
      }
    }
    return {
      id,
      type: 'danger',
      name: '未知状态'
    };
  }
};

const PAY_SUB_PROFIT_STATUS = {
  UN: {
    id: 0,
    name: '未提交',
    type: 'info'
  },
  ING: {
    id: 1,
    name: '处理中',
    type: 'primary' // 高亮
  },
  FINISH: {
    id: 2,
    name: '已成功',
    type: 'success' // 高亮
  },
  FAILED: {
    id: 4,
    name: '失败',
    type: 'danger' // 高亮
  },
  get (id) {
    for (const key in this) {
      if (Object.prototype.hasOwnProperty.call(this, key) && this[key].id === +id) {
        return this[key]
      }
    }
    return {
      id,
      type: 'danger',
      name: '未知状态'
    };
  }
};

export { ORDER_STATUS, SEND_STATUS, PAY_SUB_PROFIT_STATUS }

export function stat (params) {
  return fetch({
    url: 'order/stat',
    method: 'get',
    params
  });
}

export function getList (params) {
  return fetch({
    url: 'order',
    method: 'get',
    params
  });
}

// admin
export function getPayStat (params) {
  return fetch({
    url: 'order/stat/pay',
    method: 'get',
    params
  });
}

export function editRemark (id, remark) {
  return fetch({
    url: 'order/remark',
    method: 'post',
    data: { id, remark }
  });
}

export function ship (id, card) {
  return fetch({
    url: 'order/ship',
    method: 'post',
    data: { id, card }
  });
}

export function notify (id) {
  return fetch({
    url: 'order/notify',
    method: 'post',
    data: { id }
  });
}

export function getTodayIncome (user_id) {
  return fetch({
    url: 'order/income/today',
    method: 'get',
    params: { user_id }
  });
}

// admin
export function deleteAction (ids, delete_income, delete_balance) {
  return fetch({
    url: 'order/delete',
    method: 'post',
    data: { ids, income: delete_income, balance: delete_balance }
  });
}

// admin
export function freeze (ids, reason) {
  return fetch({
    url: 'order/freeze',
    method: 'post',
    data: { ids, reason }
  });
}

// admin
export function unfreeze (ids) {
  return fetch({
    url: 'order/unfreeze',
    method: 'post',
    data: { ids }
  });
}

// admin
export function refund (ids, refund) {
  return fetch({
    url: 'order/refund',
    method: 'post',
    data: { ids, refund }
  });
}

// admin 提交分润
export function pay_sub_profit_share (ids) {
  return fetch({
    url: 'order/pay_sub_profit_share',
    method: 'post',
    data: { ids }
  });
}

export function refundSubmit (id) {
  return fetch({
    url: 'order/refund_submit',
    method: 'post',
    data: { id }
  });
}

// admin
/**
 * 补单
 * @param {int} id
 * @param {string} trade_no 外部订单号
 * @returns {*}
 */
export function setPaid (id, trade_no) {
  return fetch({
    url: 'order/set_paid',
    method: 'post',
    data: { id, trade_no }
  });
}

export function info (id) {
  return fetch({
    url: 'order/info',
    method: 'get',
    params: { id }
  });
}

export function set_send_status (id, send_status) {
  return fetch({
    url: 'order/set_send_status',
    method: 'post',
    data: { id, send_status }
  });
}
