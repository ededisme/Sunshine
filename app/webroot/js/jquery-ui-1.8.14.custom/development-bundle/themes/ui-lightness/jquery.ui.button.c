/*
 * jQuery UI Button 1.8.14
 *
 * Copyright 2011, AUTHORS.txt (http://jqueryui.com/about)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * http://docs.jquery.com/UI/Button#theming
 */
.ui-button { display: inline-block; position: relative; padding: 0; margin-right: .1em; text-decoration: none !important; cursor: pointer; text-align: center; zoom: 1; overflow: visible; } /* the overflow property removes extra width in IE */
.ui-button-icon-only { width: 2.2em; } /* to make room for the icon, a width needs to be set here */
button.ui-button-icon-only { width: 2.4em; } /* button elements seem to need a little more width */
.ui-button-icons-only { width: 3.4em; } 
button.ui-button-icons-only { width: 3.7em; } 

/*button text element */
.ui-button .ui-button-text { display: block; line-height: 1.4;  }
.ui-button-text-only .ui-button-text { padding: .4em 1em; }
.ui-button-icon-only .ui-button-text, .ui-button-icons-only .ui-button-text { padding: .4em; text-indent: -9999999px; }
.ui-button-text-icon-primary .ui-button-text, .ui-button-text-icons .ui-button-text { padding: .4em 1em .4em 2.1em; }
.ui-button-text-icon-secondary .ui-button-text, .ui-button-text-icons .ui-button-text { padding: .4em 2.1em .4em 1em; }
.ui-button-text-icons .ui-button-text { padding-left: 2.1em; padding-right: 2.1em; }
/* no icon support for input elements, provide padding by default */
input.ui-button { padding: .4em 1em; }

/*button icon element(s) */
.ui-button-icon-only .ui-icon, .ui-button-text-icon-primary .ui-icon, .ui-button-text-icon-secondary .ui-icon, .ui-button-text-icons .ui-icon, .ui-button-icons-only .ui-icon { position: absolute; top: 50%; margin-top: -8px; }
.ui-button-icon-only .ui-icon { left: 50%; margin-left: -8px; }
.ui-button-text-icon-primary .ui-button-icon-primary, .ui-button-text-icons .ui-button-icon-primary, .ui-button-icons-only .ui-button-icon-primary { left: .5em; }
.ui-button-text-icon-secondary .ui-button-icon-secondary, .ui-button-text-icons .ui-button-icon-secondary, .ui-button-icons-only .ui-button-icon-secondary { right: .5em; }
.ui-button-text-icons .ui-button-icon-secondary, .ui-button-icons-only .ui-button-icon-secondary { right: .5em; }

/*button sets*/
.ui-buttonset { margin-right: 7px; }
.ui-buttonset .ui-button { margin-left: 0; margin-right: -.3em; }

/* workarounds */
button.ui-button::-moz-focus-inner { border: 0; padding: 0; } /* reset extra padding in Firefox */
