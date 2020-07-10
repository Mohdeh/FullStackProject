const path = require('path');

module.exports = {
  entry: {
    itemList: "./src/itemList.jsx"
  },
  output: {
    filename: "[name].dev.js",
    path: path.resolve(__dirname, "dist")
  },
  module:{
    rules:[
      {
          loader: "babel-loader",
          test: [
            /\.jsx?$/,
          ],
          include: [
            path.resolve(__dirname, "src"),
          ],
          exclude: /node_modules/,
      },
      {
        test: /\.css$/,
        use: ['style-loader', 'css-loader'],
      },
    ],
  }
};
