import path from "node:path";
import { TsconfigPathsPlugin } from "tsconfig-paths-webpack-plugin";
import { fileURLToPath } from "url";
import HtmlWebpackPlugin from "html-webpack-plugin";
import MiniCssExtractPlugin from "mini-css-extract-plugin";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const dev = process.env.DEV === '1';

export default {
    entry: {
        bundle1: './assets/scripts/bundle1.ts',
        bundle2: {
            import: './assets/scripts/bundle2.ts',
            dependOn: 'bundle1',
        },
        index: {
            import: './assets/scripts/index.ts',
            dependOn: 'bundle2',
        },
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
                test: /\.css/i,
                use: [MiniCssExtractPlugin.loader, "css-loader"],
            },
            {
                test: /\.s[ac]ss$/i,
                use: [
                    MiniCssExtractPlugin.loader,
                    'css-loader',
                    {
                        loader: 'sass-loader',
                        options: {
                            sourceMap: dev,
                        },
                    },
                ],
            },
            {
                test: /\.(woff(2)?|eot|ttf|otf)$/i,
                type: 'asset/resource',
                generator: {
                    filename: 'assets/[name][ext][query]'
                }
            }
        ],
    },
    plugins: [
        new HtmlWebpackPlugin({
            template: './assets/html/index.html',
            scriptLoading: 'blocking',
        }),
        new MiniCssExtractPlugin(),
    ],
};
