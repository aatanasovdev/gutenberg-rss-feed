module.exports = {
	mode: process.env.NODE_ENV,
	entry: ["babel-polyfill", "./src/main.js"],
	output: {
		filename: "bundle.js"
	},
	module: {
		rules: [
			{ test: /\.js$/, exclude: /node_modules/, loader: "babel-loader" }
		]
	},
	watch: process.env.NODE_ENV === 'development' ? true : false
}