require("babel-polyfill");

module.exports = {
	mode: "development",
	entry: ["babel-polyfill", "./src/main.js"],
	output: {
		filename: "bundle.js"
	},
	module: {
		rules: [
			{ test: /\.js$/, exclude: /node_modules/, loader: "babel-loader" }
		]
	}
}