module.exports = {
  presets: [
    '@vue/app'
  ],
  plugins: [
    // for element-ui loader
    [
      "component",
      {
        libraryName: "element-ui",
        styleLibraryName: "theme-chalk"
      }
    ]
  ]
};
