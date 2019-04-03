<?php
 namespace Psy\Test\CodeCleaner; use Psy\CodeCleaner\ValidFunctionNamePass; class ValidFunctionNamePassTest extends CodeCleanerTestCase { public function setUp() { $this->setPass(new ValidFunctionNamePass()); } public function testProcessInvalidFunctionCallsAndDeclarations($code) { $this->parseAndTraverse($code); } public function getInvalidFunctions() { return [ ['function array_merge() {}'], ['function Array_Merge() {}'], ['
                function psy_test_codecleaner_validfunctionnamepass_alpha() {}
                function psy_test_codecleaner_validfunctionnamepass_alpha() {}
            '], ['
                namespace Psy\\Test\\CodeCleaner\\ValidFunctionNamePass {
                    function beta() {}
                }
                namespace Psy\\Test\\CodeCleaner\\ValidFunctionNamePass {
                    function beta() {}
                }
            '], ['psy_test_codecleaner_validfunctionnamepass_gamma()'], ['
                namespace Psy\\Test\\CodeCleaner\\ValidFunctionNamePass {
                    delta();
                }
            '], ['function a() { a(); } function a() {}'], ]; } public function testProcessValidFunctionCallsAndDeclarations($code) { $this->parseAndTraverse($code); $this->assertTrue(true); } public function getValidFunctions() { return [ ['function psy_test_codecleaner_validfunctionnamepass_epsilon() {}'], ['
                namespace Psy\\Test\\CodeCleaner\\ValidFunctionNamePass {
                    function zeta() {}
                }
            '], ['
                namespace {
                    function psy_test_codecleaner_validfunctionnamepass_eta() {}
                }
                namespace Psy\\Test\\CodeCleaner\\ValidFunctionNamePass {
                    function psy_test_codecleaner_validfunctionnamepass_eta() {}
                }
            '], ['
                namespace Psy\\Test\\CodeCleaner\\ValidFunctionNamePass {
                    function psy_test_codecleaner_validfunctionnamepass_eta() {}
                }
                namespace {
                    function psy_test_codecleaner_validfunctionnamepass_eta() {}
                }
            '], ['
                namespace Psy\\Test\\CodeCleaner\\ValidFunctionNamePass {
                    function array_merge() {}
                }
            '], ['array_merge();'], ['
                namespace Psy\\Test\\CodeCleaner\\ValidFunctionNamePass {
                    function theta() {}
                }
                namespace Psy\\Test\\CodeCleaner\\ValidFunctionNamePass {
                    theta();
                }
            '], ['$test = function(){};$test()'], ['
                namespace Psy\\Test\\CodeCleaner\\ValidFunctionNamePass {
                    function theta() {}
                }
                namespace {
                    Psy\\Test\\CodeCleaner\\ValidFunctionNamePass\\theta();
                }
            '], ['function a() { a(); }'], ['
                function a() {}
                if (false) {
                    function a() {}
                }
            '], ['
                function a() {}
                if (true) {
                    function a() {}
                } else if (false) {
                    function a() {}
                } else {
                    function a() {}
                }
            '], ['
                function a() {}
                if (true):
                    function a() {}
                elseif (false):
                    function a() {}
                else:
                    function a() {}
                endif;
            '], ['
                function a() {}
                while (false) { function a() {} }
            '], ['
                function a() {}
                do { function a() {} } while (false);
            '], ['
                function a() {}
                switch (1) {
                    case 0:
                        function a() {}
                        break;
                    case 1:
                        function a() {}
                        break;
                    case 2:
                        function a() {}
                        break;
                }
            '], ]; } } 