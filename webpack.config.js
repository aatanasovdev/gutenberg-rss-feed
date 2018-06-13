module.exports = {
	mode: process.env.NODE_ENV,
	entry: ["babel-polyfill", "./src/main.js"],
	output: {
		filename: "bundle.js"
	},
	module: {
		rules: [
			{ 
				test: /\.js$/, 
				exclude: /node_modules/, 
				loader: "babel-loader" 
			},
			{
				test: /\.scss$/,
				use: [
				    "style-loader", // creates style nodes from JS strings
				    "css-loader", // translates CSS into CommonJS
				    "sass-loader" // compiles Sass to CSS
				]
			}			
		]
	},
	watch: process.env.NODE_ENV === 'development' ? true : false
}