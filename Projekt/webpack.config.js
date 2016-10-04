module.exports = {
 entry: ["./src/global.js" , "./src/index.js"],
 output: {
   filename: "./build/bundle.js"
 },
 module: {

   loaders: [
     {
       test: [/\.js$/, /\.es6$/],
       exclude: /node_modules/,
       loader: 'babel-loader',
       query: {
         presets: ['react', 'es2015']
       }
     }
   ]
 },
 resolve: {
   extensions: ['', '.js', '.es6']
 }
}
