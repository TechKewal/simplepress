// brush: "basic" aliases: ['vb']

//	This file is part of the "jQuery.Syntax" project, and is licensed under the GNU AGPLv3.
//	Copyright 2010 Samuel Williams. All rights reserved.
//	See <jquery.syntax.js> for licensing details.

Syntax.lib.vbStyleComment = {pattern: /' .*$/gm, klass: 'comment', allow: ['href']};

Syntax.register('basic', function(brush) {
	var keywords = ["AddHandler", "AddressOf", "Alias", "And", "AndAlso", "Ansi", "As", "Assembly", "Auto", "ByRef", "ByVal", "Call", "Case", "Catch", "Declare", "Default", "Delegate", "Dim", "DirectCast", "Do", "Each", "Else", "else if", "End", "Enum", "Erase", "Error", "Event", "Exit", "Finally", "For", "Function", "Get", "GetType", "GoSub", "GoTo", "Handles", "If", "Implements", "Imports", "In", "Inherits", "Interface", "Is", "Let", "Lib", "Like", "Loop", "Mod", "Module", "MustOverride", "Namespace", "New", "Next", "Not", "On", "Option", "Optional", "Or", "OrElse", "Overloads", "Overridable", "Overrides", "ParamArray", "Preserve", "Property", "RaiseEvent", "ReadOnly", "ReDim", "REM", "RemoveHandler", "Resume", "Return", "Select", "Set", "Static", "Step", "Stop", "Structure", "Sub", "SyncLock", "Then", "Throw", "To", "Try", "TypeOf", "Unicode", "Until", "When", "While", "With", "WithEvents", "WriteOnly", "Xor", "ExternalSource", "Region", "Print"];

	var operators = ["-", "&", "&=", "*", "*=", "/", "/=", "\\", "\=", "^", "^=", "+", "+=", "=", "-="];

	var types = ["CBool", "CByte", "CChar", "CDate", "CDec", "CDbl", "Char", "CInt", "Class", "CLng", "CObj", "Const", "CShort", "CSng", "CStr", "CType", "Date", "Decimal", "Variant", "String", "Short", "Long", "Single", "Double", "Object", "Integer", "Boolean", "Byte", "Char"];

	var operators = ["+", "-", "*", "/", "+=", "-=", "*=", "/=", "=", ":=", "==", "!=", "!", "%", "?", ">", "<", ">=", "<=", "&&", "||", "&", "|", "^", ".", "~", "..", ">>", "<<", ">>>", "<<<", ">>=", "<<=", ">>>=", "<<<=", "%=", "^=", "@"];

	var values = ["Me", "MyClass", "MyBase", "super", "True", "False", "Nothing", /[A-Z][A-Z0-9_]+/g, ];

	var access = ["Public", "Protected", "Private", "Shared", "Friend", "Shadows", "MustInherit", "NotInheritable", "NotOverridable"];

	brush.push(types, {klass: 'type'});
	brush.push(keywords, {klass: 'keyword', options: 'gi'});
	brush.push(operators, {klass: 'operator'});
	brush.push(access, {klass: 'access'});
	brush.push(values, {klass: 'constant'});

	brush.push(Syntax.lib.decimalNumber);

	// ClassNames (CamelCase)
	brush.push(Syntax.lib.camelCaseType);

	brush.push(Syntax.lib.vbStyleComment);

	brush.push(Syntax.lib.webLink);

	// Strings
	brush.push(Syntax.lib.doubleQuotedString);
	brush.push(Syntax.lib.stringEscape);

	brush.postprocess = function (options, html, container) {
		var queryURI = "http://social.msdn.microsoft.com/Search/en-us?query=";

		jQuery('.function', html).each(function() {
			var text = jQuery(this).text();
			jQuery(this).replaceWith(jQuery('<a>').attr('href', queryURI + encodeURIComponent(text)).text(text));
		});

		return html;
	};
});