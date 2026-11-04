const errLog = {
  state: {
    errLog: []
  },
  pushLog (log) {
    this.state.errLog.unshift(log)
  },
  clearLog () {
    this.state.errLog.splice(0, this.state.errLog.length);
  }
};

export default errLog;
