import resolve from '@rollup/plugin-node-resolve';
import replace from '@rollup/plugin-replace';
import commonjs from '@rollup/plugin-commonjs';
import svelte from 'rollup-plugin-svelte';
import babel from 'rollup-plugin-babel';
import json from '@rollup/plugin-json';
import { terser } from 'rollup-plugin-terser';
import config from 'sapper/config/rollup.js';
import pkg from './package.json';
import dotenv from 'dotenv';
import copy from 'rollup-plugin-copy';

dotenv.config();

const mode = process.env.NODE_ENV;
const dev = mode === 'development';
const legacy = !!process.env.SAPPER_LEGACY_BUILD;
const { QA_APP_URL, QA_API_URL, JIRA_TOKEN, LARK_APP_ID, LARK_APP_SECRET, LARK_CHANNEL } = process.env;

const onwarn = (warning, onwarn) => (warning.code === 'CIRCULAR_DEPENDENCY' && /[/\\]@sapper[/\\]/.test(warning.message)) || onwarn(warning);
const dedupe = importee => importee === 'svelte' || importee.startsWith('svelte/');

export default {
	client: {
		input: config.client.input(),
		output: config.client.output(),
		plugins: [
            copy({
                targets: [
                    { 
                        src: 'node_modules/@fortawesome/fontawesome-free/css/all.min.css',
                        dest: 'static/vendors/fa/css' 
                    },
                    { 
                        src: 'node_modules/@fortawesome/fontawesome-free/webfonts',
                        dest: 'static/vendors/fa' 
                    }
                ],
                copyOnce: true,
            }),
            json(),
			replace({
				preventAssignment: true,
				values: {
					'process.browser': true,
					'QA_APP_URL': JSON.stringify(QA_APP_URL),
					'QA_API_URL': JSON.stringify(QA_API_URL),
					'JIRA_TOKEN': JSON.stringify(JIRA_TOKEN),
					'LARK_APP_ID': JSON.stringify(LARK_APP_ID),
					'LARK_APP_SECRET': JSON.stringify(LARK_APP_SECRET),
					'LARK_CHANNEL': JSON.stringify(LARK_CHANNEL)
				}
			}),
			svelte({
				dev,
				hydratable: true,
				emitCss: true
			}),
			resolve({
				browser: true,
				// dedupe: ['svelte']
				dedupe
			}),
			commonjs(),
			legacy && babel({
				extensions: ['.js', '.mjs', '.html', '.svelte'],
				runtimeHelpers: true,
				exclude: ['node_modules/@babel/**'],
				presets: [
					['@babel/preset-env', {
						targets: '> 0.25%, not dead'
					}]
				],
				plugins: [
					'@babel/plugin-syntax-dynamic-import',
					['@babel/plugin-transform-runtime', {
						useESModules: true
					}]
				]
			}),

			!dev && terser({
				module: true
			})
		],

		onwarn,
	},

	server: {
		input: config.server.input(),
		output: config.server.output(),
		plugins: [
            json(),
			replace({
				preventAssignment: true,
				values: {
					'process.browser': false,
					'process.env.NODE_ENV': JSON.stringify(mode),
					'QA_APP_URL': JSON.stringify(QA_APP_URL),
					'QA_API_URL': JSON.stringify(QA_API_URL),
					'JIRA_TOKEN': JSON.stringify(JIRA_TOKEN),
					'LARK_APP_ID': JSON.stringify(LARK_APP_ID),
					'LARK_APP_SECRET': JSON.stringify(LARK_APP_SECRET),
					'LARK_CHANNEL': JSON.stringify(LARK_CHANNEL)
				}
			}),
			svelte({
				generate: 'ssr',
				dev
			}),
			resolve({
				//dedupe: ['svelte']
				dedupe
			}),
            commonjs(),
		],
		external: Object.keys(pkg.dependencies).concat(
			require('module').builtinModules || Object.keys(process.binding('natives'))
		),

		onwarn,
	},

	serviceworker: {
		input: config.serviceworker.input(),
		output: config.serviceworker.output(),
		plugins: [
            json(),
			resolve(),
			replace({
				preventAssignment: true,
				values: {
					'process.browser': true,
					'process.env.NODE_ENV': JSON.stringify(mode)
				}
			}),
            commonjs(),
			!dev && terser()
		],

		onwarn,
	}
};
