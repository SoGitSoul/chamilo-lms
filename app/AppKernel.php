<?php
/* For licensing terms, see /license.txt */

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * Class AppKernel
 */
class AppKernel extends Kernel
{
    protected $rootDir;

    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),

            new Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),

            new FOS\RestBundle\FOSRestBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle($this),

            new Oro\Bundle\MigrationBundle\OroMigrationBundle(),

            // KNP HELPER BUNDLES
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),

            new APY\DataGridBundle\APYDataGridBundle(),

            // Sonata
            //new Sonata\PageBundle\SonataPageBundle(),
            new Sonata\CoreBundle\SonataCoreBundle(),
            new Sonata\EasyExtendsBundle\SonataEasyExtendsBundle(),
            new Sonata\BlockBundle\SonataBlockBundle(),
            new Sonata\MediaBundle\SonataMediaBundle(),
            new Sonata\SeoBundle\SonataSeoBundle(),

            new Mopa\Bundle\BootstrapBundle\MopaBootstrapBundle(),

            new Liip\ThemeBundle\LiipThemeBundle(),
            new Ivory\CKEditorBundle\IvoryCKEditorBundle(),
            new FM\ElfinderBundle\FMElfinderBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),

            // User
            new FOS\UserBundle\FOSUserBundle(),
            new Sonata\UserBundle\SonataUserBundle('FOSUserBundle'),
            new Chamilo\UserBundle\ChamiloUserBundle(),

            // Sylius
            new Sylius\Bundle\SettingsBundle\SyliusSettingsBundle(),
            //new Sylius\Bundle\AttributeBundle\SyliusAttributeBundle(),
            new Sylius\Bundle\ResourceBundle\SyliusResourceBundle(),
            new Sylius\Bundle\FlowBundle\SyliusFlowBundle(),
            new WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle(),
            new Bazinga\Bundle\HateoasBundle\BazingaHateoasBundle(),

            // Chamilo
            new Chamilo\InstallerBundle\ChamiloInstallerBundle(),
            new Chamilo\CoreBundle\ChamiloCoreBundle(),
            new Chamilo\CourseBundle\ChamiloCourseBundle(),
            new Chamilo\SettingsBundle\ChamiloSettingsBundle(),
            new Chamilo\ThemeBundle\ChamiloThemeBundle(),
            new Chamilo\MediaBundle\ChamiloMediaBundle(),

            new Oneup\FlysystemBundle\OneupFlysystemBundle(),

            //new Sonata\FormatterBundle\SonataFormatterBundle(),

            // Extra
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            //new JMS\TranslationBundle\JMSTranslationBundle(),
            //new JMS\DiExtraBundle\JMSDiExtraBundle($this),
            //new JMS\AopBundle\JMSAopBundle(),
            new Bazinga\Bundle\FakerBundle\BazingaFakerBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            //$bundles[] = new Jjanvier\Bundle\CrowdinBundle\JjanvierCrowdinBundle();
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        if (in_array($this->getEnvironment(), array('test'))) {
            //$bundles[] = new Sp\BowerBundle\SpBowerBundle();
        }

        return $bundles;
    }

    /**
     * @param LoaderInterface $loader
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }

    /**
     * @return string
     */
    public function getRootDir()
    {
        if (null === $this->rootDir) {
            $r = new \ReflectionObject($this);
            $this->rootDir = str_replace('\\', '/', dirname($r->getFileName()));
        }

        return $this->rootDir;
    }

    /**
     * Returns the real root path
     * @return string
     */
    public function getRealRootDir()
    {
        return realpath($this->getRootDir().'/../').'/';
    }

    /**
     * Returns the data path
     * @return string
     */
    public function getDataDir()
    {
        return $this->getAppDir().'courses/';
    }

    /**
     * @return string
     */
    public function getAppDir()
    {
        return $this->getRealRootDir().'app/';
    }

    /**
     * @return string
     */
    public function getConfigDir()
    {
        return $this->getRealRootDir().'app/config/';
    }

    /**
     * If Chamilo is installed in my.chamilo.net return ''
     * If Chamilo is installed in my.chamilo.net/chamilo return 'chamilo'
     * @return string
     */
    public function getUrlAppend()
    {
        return $this->getContainer()->getParameter('url_append');
    }

    /**
     * @return string
     */
    public function getConfigurationFile()
    {
        return $this->getRealRootDir().'app/config/configuration.php';
    }

    /**
     * Check if system is installed
     * @return bool
     */
    public function isInstalled()
    {
        return !empty($this->getContainer()->getParameter('installed'));
    }
}

