const defaults = require("@wordpress/scripts/config/webpack.config");
const path = require('path');

module.exports = {
	...defaults,
	entry: {
		index: path.resolve(process.cwd(), 'app', 'index.js'),
	},
	output: {
		filename: '[name].js',
		path: path.resolve(process.cwd(), 'build/react'),
	},
	externals: {
		"react": "React",
		"react-dom": "ReactDOM"
	}
};