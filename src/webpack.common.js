const {resolve} = require("path");
//const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const VueLoaderPlugin = require('vue-loader/lib/plugin');
module.exports = {
    watch: true,
    watchOptions: {
        ignored: /node_modules/
    },
    entry: "./src/js/main.js",
    output: {
        path: resolve(__dirname, ''),
        filename: "./Public/js/main.js"
    },
    module: {
        rules: [

            {
                test: /\.js$/,
                exclude: /(node_modules|bower_components)/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: ['es2015']
                    }
                }

            }, {
                test: /\.s[ca]ss$/,
                exclude: /(node_modules|bower_components)/,
                use: [
                    "style-loader", // creates style nodes from JS strings
                    "css-loader", // translates CSS into CommonJS
                    "sass-loader" // compiles Sass to CSS, using Node Sass by default
                ]
            },
            {
                test: /\.vue$/,
                loader: 'vue-loader'
            },
            {
                test: /\.css$/,
                use: [
                    'vue-style-loader',
                    'css-loader'
                ]
            }
            /*,
            {
                test: /\.css$/,
                use: [
                    MiniCssExtractPlugin.loader,
                    "css-loader"
                ]
            }*/
        ]
    },
    plugins: [
    // make sure to include the plugin!
    new VueLoaderPlugin()
]

    /*,
    plugins: [
        new MiniCssExtractPlugin({
            filename: "stylemf.css",
        })
    ],*/
}