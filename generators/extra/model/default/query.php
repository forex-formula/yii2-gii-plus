<?php

use yii\helpers\Inflector;

/* @var $this yii\web\View */
/* @var $generator yii\gii\plus\generators\extra\model\Generator */

$primaryKey = $generator->getPrimaryKey();

echo '<?php';
?>


namespace <?php echo $generator->getNewQueryNamespace(); ?>;

<?php echo $generator->getQueryUseDirective(); ?>

class <?php echo $generator->getNewQueryName(); ?> extends <?php echo $generator->getQueryAlias(); ?>

{

    /**
     * @inheritdoc
     */
    /*public function init()
    {
        parent::init();
    }*/

    /**
     * @return self
     */
<?php if (count($primaryKey) == 1) { ?>
    public function pk($<?php echo Inflector::variablize($primaryKey[0]); ?>)
    {
        return $this->andWhere([$this->getAlias() . '.<?php echo $primaryKey[0]; ?>' => $<?php echo Inflector::variablize($primaryKey[0]); ?>]);
    }
<?php } else { ?>
    public function pk($<?php echo implode(', $', array_map(['yii\helpers\Inflector', 'variablize'], $primaryKey)); ?>)
    {
        $alias = $this->getAlias();
        return $this->andWhere([
<?php foreach ($primaryKey as $i => $column) { ?>
            $alias . '.<?php echo $column; ?>' => $<?php echo Inflector::variablize($column); if ($i < count($primaryKey) - 1) { echo ','; } ?>

<?php } ?>
        ]);
    }
<?php } ?>
}
