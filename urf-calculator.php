<?php

if(!defined('ABSPATH')) exit; // Exit if accessed directly

class KMI_URF_Calculator
{
    private $_variable_arr = array();
    private $_message_arr = array();
    
    public function __construct()
    {
        $this->_Setup_Shortcodes();
        $this->_Setup_Action_Hooks();
    }
    
    public function Initialization()
    {
        if(isset($_POST['kmi_urf_calculator']['calculate'])) // Calculate ultrasonic range
        {
            $this->_Calculate_URF($_POST['kmi_urf_calculator']['ticks_per_second'], $_POST['kmi_urf_calculator']['ticks_delay']);
        }
        else if(isset($_POST['kmi_urf_calculator']['reset'])) // Reset ultrasonic range finder form
        {
            $_POST['kmi_urf_calculator']['ticks_per_second'] = '';
            $_POST['kmi_urf_calculator']['ticks_delay'] = '';
        }
    }
    
    public function Add_Styles_And_Scripts()
    {
        if(!wp_style_is('kmi_global_style', 'registered'))
        {
            wp_register_style('kmi_global_style', plugins_url('css/global.css', __FILE__));
        }
        if(!wp_style_is('kmi_global_style', 'enqueued'))
        {
            wp_enqueue_style('kmi_global_style');
        }
        
        if(!wp_script_is('kmi_urf_calculator_script', 'registered'))
        {
            wp_register_script('kmi_urf_calculator_script', plugins_url('js/kmi-urf-calculator.js', __FILE__));
        }
        if(!wp_script_is('kmi_urf_calculator_script', 'enqueued'))
        {
            wp_enqueue_script('kmi_urf_calculator_script');
        }
        
        wp_localize_script('kmi_urf_calculator_script', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
    }
    
    public function URF_Calculator_Form()
    {
        $ticks_per_sec = !empty($this->_variable_arr['ticks_per_sec']) ? $this->_variable_arr['ticks_per_sec'] : $_POST['kmi_urf_calculator']['ticks_per_second'];
        $ticks_delay = !empty($this->_variable_arr['ticks_delay']) ? $this->_variable_arr['ticks_delay'] : $_POST['kmi_urf_calculator']['ticks_delay'];
        
        ob_start();
        ?>
        <form class="kmi-form kmi-one-column align-center" method="POST" action="">
            <p><input type="text" class="kmi-two-columns" placeholder="Number of ticks per second" value="<?php echo $ticks_per_sec; ?>" id="kmi_urf_calc_ticks_per_sec" name="kmi_urf_calculator[ticks_per_second]" /></p>
            <p><input type="text" class="kmi-two-columns" placeholder="Number of tiks delay" value="<?php echo $ticks_delay; ?>" id="kmi_urf_calc_ticks_delay" name="kmi_urf_calculator[ticks_delay]" /></p>
            <p><input type="text" class="kmi-two-columns" placeholder="Value" value="<?php echo $this->_variable_arr['urf_calc_result']; ?>" id="kmi_urf_calc_result" readonly="readonly" name="kmi_urf_calculator[value]" /></p>
            <p>
                <input type="submit" id="kmi_calculate_urf_btn" class="kmi-two-columns" value="Calculate" name="kmi_urf_calculator[calculate]" />
                <input type="submit" id="kmi_reset_urf_btn" class="kmi-two-columns" value="Reset" name="kmi_urf_calculator[reset]" />
            </p>
        </form>
        <?php
        return ob_get_clean();
    }
    
    /*
     * Calculate ultrasonic range finder through ajax call.
     */
    public function Calculate_URF()
    {
        $response = array();
        
        // $_POST data
        $ticks_per_sec = $_POST['kmi_urf_calculator']['ticks_per_second'];
        $ticks_delay = $_POST['kmi_urf_calculator']['ticks_delay'];
        
        // Do the calculation
        $this->_Calculate_URF($ticks_per_sec, $ticks_delay);
        
        if(!empty($this->_variable_arr['urf_calc_result']))
            $response['urf_calc_result'] = $this->_variable_arr['urf_calc_result'];
        
        echo json_encode($response);
        wp_die();
    }
    
    /*
     * Calculate ultrasonic range finder.
     */
    private function _Calculate_URF($ticks_per_sec='0', $ticks_delay='0')
    {
        // Check for valid float value
        $this->_variable_arr['ticks_per_sec'] = filter_var($ticks_per_sec, FILTER_VALIDATE_FLOAT) ? $ticks_per_sec : '0';
        $this->_variable_arr['ticks_delay'] = filter_var($ticks_delay, FILTER_VALIDATE_FLOAT) ? $ticks_delay : '0';
        
        if(!empty($this->_variable_arr['ticks_per_sec']) && !empty($this->_variable_arr['ticks_delay']))
        {
            $this->_variable_arr['urf_calc_result'] = ($this->_variable_arr['ticks_delay'] * (1 / $this->_variable_arr['ticks_per_sec']) * 1127) / 2;
        }
    }
    
    private function _Setup_Shortcodes()
    {
        // Ultrasonic range finder calculator
        add_shortcode('kmi_urf_calculator', array($this, 'URF_Calculator_Form'));
    }
    
    private function _Setup_Action_Hooks()
    {
        // Executes all front-end processes required for the plugin
        add_action('init', array($this, 'Initialization'));
        // Add front-end css and scripts
        add_action('wp_enqueue_scripts', array($this, 'Add_Styles_And_Scripts'));
        // Ajax functions
        add_action('wp_ajax_calculate_urf', array($this, 'Calculate_URF'));
        add_action('wp_ajax_nopriv_calculate_urf', array($this, 'Calculate_URF'));
    }
}

$kmi_urf_calculator = new KMI_URF_Calculator();