import $ from 'jquery';

/**
 * Singleton class
 * Handle Semantic-ui form functionality throughout the app.
 */

class FormService {

  static getInstance() {
    return this.instance;
  }


  constructor() {
    if (!this.instance) {
      this.instance = this;
      this.formSettings = $.fn.form.settings;
    }
    return this.instance;
  }

  /**
   * Setup semantic-ui form callback with this service.
   * @param settings
   */
  setService(settings) {
    settings.rules.visible = this.isVisible;
    settings.rules.notEmpty = settings.rules.empty;
  }

  /**
   * Visibility rule.
   *
   * @returns {boolean | jQuery}
   */
  isVisible() {
    return $(this).is(':visible');
  }

  /**
   * Validate a field using semantic-ui validate rule function.
   *
   * @param form  Form containing the field.
   * @param field Name of field
   * @param rule  Rule to apply test.
   * @returns {*|boolean}
   */
  validateField(form, fieldName, rule) {
    rule = this.normalizeRule(rule);
    const ruleName = this.getRuleName(rule);
    const ruleFunction = this.getRuleFunction(ruleName);
    const $field = this.getField(form, fieldName);
    if ($field) {
      const value = $field.val();
    }
    const ancillary = this.getAncillaryValue(rule);
    if (ruleFunction) {
      return ruleFunction.call($field, value, ancillary);
    } else {
      console.log('this rule does not exist: '+ruleName);
      return false;
    }
  }

  normalizeRule(rule) {
    if (typeof rule === 'string') {
      return {type: rule, value:null};
    }
    return rule;
  }

  getField(form, identifier) {
    if(form.find('#' + identifier).length > 0 ) {
      return form.find('#' + identifier);
    }
    else if( form.find('[name="' + identifier +'"]').length > 0 ) {
      return form.find('[name="' + identifier +'"]');
    }
    else if( form.find('[name="' + identifier +'[]"]').length > 0 ) {
      return form.find('[name="' + identifier +'[]"]');
    }
    return false;
  }

  getRuleFunction(rule) {
    return this.formSettings.rules[rule];
  }

  getAncillaryValue(rule) {
    //must have a rule.value property and must be a bracketed rule.
    if(!rule.value && !this.isBracketedRule(rule)) {
      return false;
    }
    return (rule.value === undefined || rule.value === null)
      ? rule.type.match(this.formSettings.regExp.bracket)[1] + ''
      : rule.value
      ;
  }

  getRuleName(rule) {
    if( this.isBracketedRule(rule) ) {
      return rule.type.replace(rule.type.match(this.formSettings.regExp.bracket)[0], '');
    }
    return rule.type;
  }

  isBracketedRule(rule) {
    return (rule.type && rule.type.match(this.formSettings.regExp.bracket));
  }
}

const formService = new FormService();
Object.freeze(formService);

export default formService;