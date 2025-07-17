import path from "node:path";
import { TsconfigPathsPlugin } from "tsconfig-paths-webpack-plugin";
import { fileURLToPath } from "url";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const dev = process.env.DEV === '1';

export default {
    entry: {
        index: './assets/html/index.html',
    },
    mode: dev ? 'development' : 'production',
    devtool: dev ? 'inline-source-map' : false,
    output: {
        path: path.resolve(__dirname, './html'),
        assetModuleFilename: "[name][ext]",
    },
    resolve: {
        // Add `.ts` and `.tsx` as a resolvable extension.
        extensions: [".ts", ".tsx", ".js"],
        // Add support for TypeScripts fully qualified ESM imports.
        extensionAlias: {
            ".js": [".js", ".ts"],
            ".cjs": [".cjs", ".cts"],
            ".mjs": [".mjs", ".mts"]
        },
        plugins: [new TsconfigPathsPlugin({ configFile: "./assets/scripts/tsconfig.json" })],
    },
    module: {
        rules: [
            {
                test: /\.([cm]?ts|tsx)$/,
                use: ["ts-loader"],
            },
            {
                test: /\.html$/,
                type: "asset/resource",
                generator: {
                    filename: "[name][ext]",
                },
            },
            {
                test: /\.html$/i,
                loader: "html-loader",
            },
            {
                test: /\.css/i,
                loader: "css-loader",
            }
        ],
    },
};
