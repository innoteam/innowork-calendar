<?php
class InnoworkEventFactory {
	function RemoveExternalEvent($type, $id, $extdata = '') {
		if (strlen($type) and $type != '%' and (int) $id) {
			$query = 'SELECT id FROM innowork_calendar WHERE exttype='.\Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentDomain()->getDataAccess()->formatText($type).' AND extid='. (int) $id;
			if (strlen($extdata))
				$query.= ' AND extdata='.\Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentDomain()->getDataAccess()->formatText($extdata);
			$items_query = \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentDomain()->getDataAccess()->execute($query);

			while (!$items_query->eof) {
				$event = new InnoworkEvent(\Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getDataAccess(), \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentDomain()->getDataAccess(), $items_query->getFields('id'));
				$event->Remove();
				$items_query->MoveNext();
			}
		}
	}
}
?>