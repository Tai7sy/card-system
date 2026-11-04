module.exports = {
  presets: [
    '@vue/cli-plugin-babel/preset'
  ],
  plugins: [
    // for muse-ui loader
    ["import", {
      libraryName: "muse-ui",
      libraryDirectory: "lib",
      camel2DashComponentName: false
    }]
  ]
};
