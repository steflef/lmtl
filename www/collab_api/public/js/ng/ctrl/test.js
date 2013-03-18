'use strict';

describe('Controller: NameCtrl', function() {

  // load the controller's module
  beforeEach(module('Module'));

  var NameCtrl,
    scope;

  // Initialize the controller and a mock scope
  beforeEach(inject(function($controller) {
    scope = {};
    NameCtrl = $controller('NameCtrl', {
      $scope: scope
    });
  }));

  it('should ', function() {
      expect(scope.).toBe();
    });
});