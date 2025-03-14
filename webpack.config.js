const defaultConfig = require( './node_modules/@wordpress/scripts/config/webpack.config' );
const path = require( 'path' );
const IgnoreEmitPlugin = require( 'ignore-emit-webpack-plugin' );
const MiniCSSExtractPlugin = require( 'mini-css-extract-plugin' );

const isProduction = process.env.NODE_ENV === 'production';
const mode = isProduction ? 'production' : 'development';

const jsFiles = {
	form: path.resolve( process.cwd(), 'assets/js', 'form.js' ),
	'multi-field': path.resolve( process.cwd(), 'assets/js', 'multi-field.js' ),
	validation: path.resolve( process.cwd(), 'assets/js', 'validation.js' ),
};
const scssFiles = {
	form: path.resolve( process.cwd(), 'assets/style', 'form.scss' ),
	'twenty-twenty-five': path.resolve(
		process.cwd(),
		'assets/style',
		'twenty-twenty-five.scss'
	),
	'twenty-twenty-four': path.resolve(
		process.cwd(),
		'assets/style',
		'twenty-twenty-four.scss'
	),
	'twenty-twenty-three': path.resolve(
		process.cwd(),
		'assets/style',
		'twenty-twenty-three.scss'
	),
	'twenty-twenty-two': path.resolve(
		process.cwd(),
		'assets/style',
		'twenty-twenty-two.scss'
	),
};

module.exports = [
	// blocks
	{ ...defaultConfig },
	// JavaScript minification
	{
		mode: mode,
		devtool: ! isProduction ? 'source-map' : 'hidden-source-map',
		entry: jsFiles,
		output: {
			filename: '[name].min.js',
			path: path.resolve( process.cwd(), 'assets/js/build' ),
		},
		optimization: {
			minimize: true,
			minimizer: defaultConfig.optimization.minimizer,
		},
	},
	// compiled + minified CSS file
	{
		mode: mode,
		devtool: ! isProduction ? 'source-map' : 'hidden-source-map',
		entry: scssFiles,
		output: {
			path: path.resolve( process.cwd(), 'assets/style/build' ),
		},
		module: {
			rules: [
				{
					test: /\.(sc|sa)ss$/,
					use: [
						MiniCSSExtractPlugin.loader,
						{
							loader: 'css-loader',
							options: {
								sourceMap: ! isProduction,
								url: false,
							},
						},
						{
							loader: 'sass-loader',
							options: {
								sourceMap: ! isProduction,
								sassOptions: {
									minimize: true,
									outputStyle: 'compressed',
								},
							},
						},
					],
				},
			],
		},
		plugins: [
			new MiniCSSExtractPlugin( { filename: '[name].min.css' } ),
			new IgnoreEmitPlugin( [ '.js' ] ),
		],
	},
	// compiled CSS
	{
		mode: mode,
		devtool: ! isProduction ? 'source-map' : 'hidden-source-map',
		entry: scssFiles,
		output: {
			path: path.resolve( process.cwd(), 'assets/style/build' ),
		},
		module: {
			rules: [
				{
					test: /\.(sc|sa)ss$/,
					use: [
						MiniCSSExtractPlugin.loader,
						{
							loader: 'css-loader',
							options: {
								sourceMap: ! isProduction,
								url: false,
							},
						},
						{
							loader: 'sass-loader',
							options: {
								sourceMap: ! isProduction,
								sassOptions: {
									minimize: false,
									outputStyle: 'expanded',
								},
							},
						},
					],
				},
			],
		},
		plugins: [
			new MiniCSSExtractPlugin( { filename: '[name].css' } ),
			new IgnoreEmitPlugin( [ '.js' ] ),
		],
	},
];
