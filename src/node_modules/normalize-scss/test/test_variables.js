'use strict';

var sassyTest = new SassyTest();

describe('Configuration variables', function() {
  before(function(done) {
    sassyTest.configurePaths({
      fixtures: path.join(__dirname, 'fixtures'),
      includePaths: [path.join(__dirname, '../sass')]
    });
    done();
  });

  describe('default values', function() {
    it('should limit output to the same output as normalize.css', function() {
      return sassyTest.renderFixture('variables/default');
    });
  });

  describe('$base-* and $h*-font-size', function() {
    it('should alter the font, font size, and line-height', function() {
      return sassyTest.renderFixture('variables/font');
    });
  });

  describe('$indent-amount', function() {
    it('should alter the indent amount of elements', function() {
      return sassyTest.renderFixture('variables/indent-amount');
    });
  });

  describe('$indent-amount and $normalize-vertical-rhythm', function() {
    it('should alter the indent amount of elements', function() {
      return sassyTest.renderFixture('variables/indent-amount-and-vertical-rhythm');
    });
  });
});
