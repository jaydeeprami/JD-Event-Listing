/**
 *  JD Event Listing Gulp File.
 *
 *  Used for automating development tasks.
 */

/* Modules (Can be installed with npm install command using package.json)
 ------------------------------------- */
var gulp = require( 'gulp' ),
	imagemin = require( 'gulp-imagemin' ),
	livereload = require( 'gulp-livereload' ),
	cssmin = require( 'gulp-cssmin' ),
	notify = require( 'gulp-notify' ),
	rename = require( 'gulp-rename' ),
	uglify = require( 'gulp-uglify' ),
	sort = require( 'gulp-sort' ),
	checktextdomain = require( 'gulp-checktextdomain' ),
	wpPot = require( 'gulp-wp-pot' ),
	readme = require( 'gulp-readme-to-markdown' );

/* Paths
 ------------------------------------- */
var source_paths = {
	admin_styles: [ './assets/css/jd-event-listing-admin.css' ],
	frontend_styles: [ './assets/css/jd-event-listing-frontend.css' ],
	scripts: [
		'./assets/js/*.js',
		'!./assets/js/*.min.js'
	]
};

/* POT file task
 ------------------------------------- */
gulp.task( 'pot', function() {
	return gulp.src( '**/*.php' )
		.pipe( sort() )
		.pipe( wpPot( {
			package: 'JD_Event_Listing',
			domain: 'jd-event-listing', // Text-domain.
			destFile: 'jd-event-listing.pot',
			bugReport: 'https://github.com/jaydeeprami/JD-Event-Listing/issues/new',
			lastTranslator: '',
			team: 'Jaydeep <jaydeep.ramii@gmail.com>'
		} ) )
		.pipe( gulp.dest( 'languages' ) );
} );

/* Text-domain task
 ------------------------------------- */
gulp.task( 'textdomain', function() {
	var options = {
		text_domain: 'jd-event-listing',
		keywords: [
			'__:1,2d',
			'_e:1,2d',
			'_x:1,2c,3d',
			'esc_html__:1,2d',
			'esc_html_e:1,2d',
			'esc_html_x:1,2c,3d',
			'esc_attr__:1,2d',
			'esc_attr_e:1,2d',
			'esc_attr_x:1,2c,3d',
			'_ex:1,2c,3d',
			'_n:1,2,4d',
			'_nx:1,2,4c,5d',
			'_n_noop:1,2,3d',
			'_nx_noop:1,2,3c,4d'
		],
		correct_domain: true
	};
	return gulp.src( '**/*.php' )
		.pipe( checktextdomain( options ) );
} );

/* Convert WordPress readme file to readme.md
 ------------------------------------- */
gulp.task( 'readme', function() {
	gulp.src( [ 'readme.txt' ] )
		.pipe( readme( {
			details: false,
			screenshot_ext: [
				'jpg',
				'jpg',
				'png'
			],
			extract: {}
		} ) )
		.pipe( gulp.dest( '.' ) );
} );

/* Default Gulp task
 ------------------------------------- */
gulp.task( 'default', function() {
	// Run all the tasks!
	gulp.start( 'textdomain', 'pot', 'admin_styles', 'frontend_styles', 'scripts', 'readme' );
} );

/* Admin CSS Task
 ------------------------------------- */
gulp.task( 'admin_styles', function() {

	gulp.src( source_paths.admin_styles )
		.pipe( rename( 'jd-event-listing-admin.css' ) )
		.pipe( gulp.dest( './assets/css' ) )
		.pipe( rename( 'jd-event-listing-admin.min.css' ) )
		.pipe( cssmin() )
		.pipe( gulp.dest( './assets/css' ) )
		.pipe( livereload() )
		.pipe( notify( {
			message: 'Admin styles task complete!',
			onLast: true // Only notify on completion of task.
		} ) );
} );

/* Frontend CSS Task
 ------------------------------------- */
gulp.task( 'frontend_styles', function() {

	gulp.src( source_paths.frontend_styles )
		.pipe( rename( 'jd-event-listing-frontend.css' ) )
		.pipe( gulp.dest( './assets/css' ) )
		.pipe( rename( 'jd-event-listing-frontend.min.css' ) )
		.pipe( cssmin() )
		.pipe( gulp.dest( './assets/css' ) )
		.pipe( livereload() )
		.pipe( notify( {
			message: 'Frontend styles task complete!',
			onLast: true // Only notify on completion of task.
		} ) );
} );

/* Concatenate & Minify JS
 ------------------------------------- */
gulp.task( 'scripts', function() {
	return gulp.src( source_paths.scripts )
		.pipe( uglify( { preserveComments: 'false' } ) )
		.pipe( rename( { suffix: '.min' } ) )
		.pipe( gulp.dest( 'assets/js' ) )
		.pipe( notify( {
			message: 'Scripts task complete!',
			onLast: true // Only notify on completion of task (prevents multiple notifications per file).
		} ) )
		.pipe( livereload() );
} );

/* Image Minify Task
 ------------------------------------- */
gulp.task( 'image_minify', function() {
	gulp.src( './assets/images/*' )
		.pipe( imagemin() )
		.pipe( gulp.dest( './assets/images' ) );
} );