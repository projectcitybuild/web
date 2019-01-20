'use strict';

describe('Fork versions', function() {
  describe('Default fork', function() {
    it('should render properly', function() {
      var sassyTest = new SassyTest({
        fixtures: path.join(__dirname, 'fixtures/fork-versions'),
        includePaths: [
          // Path to fork version.
          path.join(__dirname, '../fork-versions/default')
        ]
      });
      return sassyTest.renderFixture('default');
    });
  });

  describe('Compass fork', function() {
    it('should render properly', function() {
      var sassyTest = new SassyTest({
        fixtures: path.join(__dirname, 'fixtures/fork-versions/deprecated-compass'),
        includePaths: [
          // Path to fork version.
          path.join(__dirname, '../fork-versions/deprecated-compass')
        ]
      });
      return sassyTest.renderFixture('./');
    });
  });

  describe('Typey fork', function() {
    it('should render properly', function() {
      var sassyTest = new SassyTest({
        fixtures: path.join(__dirname, 'fixtures/fork-versions'),
        includePaths: [
          // Path to fork version.
          path.join(__dirname, '../fork-versions/typey'),
          // Path to the fork's dependencies.
          path.dirname(require.resolve('typey'))
        ]
      });
      return sassyTest.renderFixture('typey');
    });
  });

  describe('Typey, Chroma and KSS fork', function() {
    it('should render properly', function() {
      var sassyTest = new SassyTest({
        fixtures: path.join(__dirname, 'fixtures/fork-versions'),
        includePaths: [
          // Path to fork version.
          path.join(__dirname, '../fork-versions/typey-chroma-kss'),
          // Path to the fork's dependencies.
          path.dirname(require.resolve('chroma-sass')),
          path.dirname(require.resolve('typey'))
        ]
      });
      return sassyTest.renderFixture('typey-chroma-kss');
    });
  });
});
