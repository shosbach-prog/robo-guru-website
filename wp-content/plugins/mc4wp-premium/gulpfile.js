const gulp = require('gulp');
const uglify = require('gulp-uglify');
const rename = require('gulp-rename');
const cssmin = require('gulp-clean-css');
const source = require('vinyl-source-stream');
const browserify = require('browserify');
const streamify = require('gulp-streamify');

gulp.task('css', () => gulp.src('./*/assets/src/css/*.css')
  .pipe(cssmin())
  .pipe(rename((path) => {
    path.dirname += '/../../css';
  }))
  .pipe(gulp.dest('./')));

const bundles = [
  'activity-dashboard-widget/assets/src/js/dashboard-widget.js',
  'ajax-forms/assets/src/js/ajax-forms.js',
  'autocomplete/assets/src/js/autocomplete.js',
  'ecommerce3/assets/src/js/admin.js',
  'ecommerce3/assets/src/js/cart.js',
  'ecommerce3/assets/src/js/tracker.js',
  'email-notifications/assets/src/js/admin.js',
  'logging/assets/src/js/admin-statistics.js',
  'styles-builder/assets/src/js/styles-builder.js',
  'user-sync/assets/src/js/admin.js',
];
gulp.task('js', gulp.parallel(bundles.map((entry) => () => {
  const file = entry.split('/').pop();
  const dest = entry.replace('/browserify/', '/js/').replace(file, '');

  let pipeline = browserify({ entries: entry })
    .transform('babelify', {
      presets: [
        ['@babel/preset-env', { forceAllTransforms: true }],
      ],
    })
    .bundle()
    .pipe(source(file));

  if (process.NODE_ENV !== 'development') {
    pipeline = pipeline
      .pipe(streamify(uglify()));
  }

  return pipeline.pipe(rename((path) => {
    path.dirname += '/../../js';
  }))
    .pipe(gulp.dest(dest));
})));

gulp.task('default', gulp.parallel('css', 'js'));
gulp.task('watch', gulp.series('default', () => {
  gulp.watch('./*/assets/src/css/*.css', gulp.series('css'));
  gulp.watch('./*/assets/src/js/**/*.js', gulp.series('js'));
}));
